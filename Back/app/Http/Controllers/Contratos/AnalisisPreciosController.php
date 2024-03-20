<?php

namespace App\Http\Controllers\Contratos;

use AnalisisPrecios\AnalisisItem;
use AnalisisPrecios\AnalisisPrecios;
use AnalisisPrecios\Categoria\CategoriaModelExtended;
use AnalisisPrecios\Categoria\Componente\ComponenteModelExtended;
use AnalisisPrecios\Categoria\Rendimiento;
use AnalisisPrecios\Categoria\Unidad;
use App\Jobs\CalculoItemAdenda;
use Contrato\Contrato;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Itemizado\Item;
use Indice\ValorIndicePublicado;
use Log;
use Maatwebsite\Excel\Facades\Excel;
use Response;
use View;
use DateTime;

class AnalisisPreciosController extends ContratosControllerExtended
{

    public function __construct()
    {
        View::share('ayuda', 'contrato');
        $this->middleware('auth', ['except' => 'logout']);
    }

//#region Analisis de Precios

    /**
     * @param  int  $analisis_precios_id
     */
    public function editCoeficienteK($analisis_precios_id)
    {
        $analisis_precios = AnalisisPrecios::find($analisis_precios_id);

        if (!$analisis_precios->permite_editar) {
            return redirect()->route('contratos.index');
        }

        if (!Auth::user()->puedeVerCausante($analisis_precios->contrato->causante_id)) {
            return redirect()->route('contratos.index');
        }

        return view('contratos.contratos.show.analisis_precios.modals.edit_coeficiente', compact('analisis_precios'));
    }

    /**
     * @param  int  $analisis_precios_id
     * @param  \Illuminate\Http\Request  $request
     */
    public function updateCoeficienteK($analisis_precios_id, Request $request)
    {
        $analisis_precios = AnalisisPrecios::find($analisis_precios_id);

        if (!$analisis_precios->permite_editar) {
            $jsonResponse['status'] = false;
            $jsonResponse['url'] = route('contratos.index');
            $jsonResponse['message'] = [trans('mensajes.error.permisos')];
            Session::flash('error', trans('mensajes.error.permisos'));
            return response()->json($jsonResponse);
        }

        if (!Auth::user()->puedeVerCausante($analisis_precios->contrato->causante_id)) {
            $jsonResponse['status'] = false;
            $jsonResponse['url'] = route('contratos.index');
            $jsonResponse['message'] = [trans('mensajes.error.permisos')];
            Session::flash('error', trans('mensajes.error.permisos'));
            return response()->json($jsonResponse);
        }

        $coeficiente_k = $this->dosDecToDB($request->get('coeficiente_k'));
        $inputVar = explode(".", $coeficiente_k);

        $errores = array();
        if (!isset($inputVar[1])) {
            $inputVar[1] = "0000";
        }

        if (strlen($inputVar[0]) > 1) {
            $errores['coeficiente_k'] = trans('mensajes.error.max_number_coeficiente_k');
        }

        if ($inputVar[0] < 1) {
            $errores['coeficiente_k'] = trans('mensajes.error.min_number_coeficiente_k');
        }

        if (strlen($inputVar[1]) > 4) {
            $errores['coeficiente_k'] = trans('mensajes.error.max_decimal_4');
        }


        if (sizeof($errores) > 0) {
            $jsonResponse['status'] = false;
            $jsonResponse['errores'] = $errores;
            Session::flash('error', trans('mensajes.error.revisar'));
            $jsonResponse['message'] = [trans('mensajes.error.revisar')];
            return response()->json($jsonResponse);
        }

        $analisis_precios->coeficiente_k = $coeficiente_k;
        $analisis_precios->save();

        $jsonResponse['status'] = true;
        $jsonResponse['close_modal'] = '#modalCoeficiente';
        $jsonResponse['container'] = '#analisis_precios_container';
        $jsonResponse['html'] = app('ContratosController')->getViews($analisis_precios->contrato->id,
          'analisis_precios')->getData()->view;
        $jsonResponse['message'] = [trans('analisis_precios.coeficiente_k').trans('mensajes.success.editado')];

        return response()->json($jsonResponse);
    }

    /**
     * @param  int  $analisis_precios_id
     * @param  string  $accion
     */
    public function updateOrStore($analisis_precios_id, $accion)
    {
        $analisis_precios = AnalisisPrecios::find($analisis_precios_id);
        $analisis_precios->load('contrato_moneda.contrato.causante');
        $user = Auth::user();

        $causante = null;

        if (!empty($analisis_precios->contrato_moneda->contrato->causante))
            $causante = $analisis_precios->contrato_moneda->contrato->causante;


        if (empty($causante) || !$user->puedeVerCausante($causante->id)) {
            Log::error(trans('index.error403'), ['User' => $user, 'Intenta' => 'analisis_precios-edit'.$accion]);
            $jsonResponse['message'] = [trans('index.error403')];
            $jsonResponse['permisos'] = true;
            $jsonResponse['status'] = false;
            return response()->json($jsonResponse);
        }

        if (!in_array($accion, $analisis_precios->acciones_posibles)) {
            Log::error(trans('index.error403'), ['User' => $user, 'Intenta' => 'analisis_precios-edit' . $accion]);
            $jsonResponse['message'] = [trans('index.error403')];
            $jsonResponse['permisos'] = true;
            $jsonResponse['status'] = false;
            return response()->json($jsonResponse);
        }

        $errores = [];

        if (!$analisis_precios->es_redeterminacion) {
            foreach ($analisis_precios->analisis_items as $valueAnalisisItem) {
                $puedeGuardar = $valueAnalisisItem->puedeGuardar($accion);

                if (!$puedeGuardar['status'] && !empty($puedeGuardar['errores']))
                    $errores[$valueAnalisisItem->id] = $puedeGuardar['errores'];
            }
        }

        if (sizeof($errores) > 0) {
            $jsonResponse['status'] = false;
            $jsonResponse['errores'] = $errores;
            $jsonResponse['message'] = [];
            $jsonResponse['error_container'] = '#errores-analisis_precios';

            return response()->json($jsonResponse);
        }

        $dobleFirma = $causante->doble_firma;
        $firmaAr = $causante->jefe_contrato_ar;
        $firmaPy = $causante->jefe_contrato_py;

        if ($dobleFirma) {
            if ($user->id == $firmaAr)
                $analisis_precios->firma_ar = $user->id;

            if ($user->id == $firmaPy)
                $analisis_precios->firma_py = $user->id;

            $analisis_precios->save();
        }

        if ($dobleFirma && in_array($accion, ['firmar', 'firma', 'aprobar_precios']) ) {
            if ($analisis_precios->firma_ar && $analisis_precios->firma_py)
                $accion = "aprobar_precios";
            elseif ($analisis_precios->firma_ar  ||  $analisis_precios->firma_py)
                $accion = "firma";
            else {
                $accion = "a_firmar";
            }
        }

        foreach ($analisis_precios->analisis_items as $valueAnalisisItem)
            $valueAnalisisItem->createProximaInstanciaHistorial($accion);

        $analisis_precios->createProximaInstanciaHistorial($accion);

        $jsonResponse['status'] = true;
        $jsonResponse['borrador'] = 0;
        $jsonResponse['message'] = [trans('analisis_precios.mensajes.success.' . $accion)];


        if (!$analisis_precios->es_redeterminacion) {
            $jsonResponse['url'] = route('contratos.ver.incompleto', [
              'id' => $analisis_precios->contrato->id,
              'accion' => 'analisis_precios'
            ]);
        }
        else {
            $jsonResponse['url'] = route('empalme.redeterminacion.ver', [
              'redeterminacion_id' => $analisis_precios->redeterminacion_id
            ]);
        }

        return response()->json($jsonResponse);
    }

    public function exportar(Request $request)
    {
        $input = $request->all();
        $contrato = Contrato::find($input['excel_input']);

        Excel::create(trans('forms.analisis_precios').'_'.$contrato->numero_contrato,
          function ($excel) use ($request, $input, $contrato) {

              foreach ($contrato->contratos_monedas as $valueContratoMoneda) {
                  $excel->sheet($valueContratoMoneda->moneda->nombre_simbolo,
                    function ($sheet) use ($valueContratoMoneda, $input, $contrato) {

                        $analisis_precios = $contrato->analisis_precios[0];
                        if ($input['version'] == 'vigente' && $contrato->has_itemizado_vigente) {
                            $results = Item::whereItemizadoId($analisis_precios->itemizado_actual->id)->orderBy('codigo',
                              'ASC')->get();
                        }
                        else {
                            $results = Item::whereItemizadoId($analisis_precios->itemizado_actual->id)->orderBy('codigo',
                              'ASC')->get();
                        }

                        $arr_excel = $results->map(function ($item) use ($analisis_precios) {
                            $arr = array();

                            if ($item->is_ajuste_alzado) {
                                $es_ajuste_alzado = true;
                            }
                            else {
                                $es_ajuste_alzado = false;
                            }

                            $arr[trans('forms.codigo')] = $item->codigo;
                            $arr[trans('forms.item')] = $item->item ? $item->item : $item->codigo;
                            $arr[trans('forms.descripcion')] = $item->descripcion;

                            if (!$es_ajuste_alzado) {
                                $arr[trans('forms.cantidad')] = $item->cantidad;
                                $arr[trans('forms.unidad_medida_um')] = $item->unidad_medida_nombre;
                            }
                            else {
                                $arr[trans('forms.cantidad')] = (float) 1;
                                $arr[trans('forms.unidad_medida_um')] = trans('forms.ajuste_alzado');
                            }

                            $analisis_item = $analisis_precios->getAnalisisItem($item->id);
                            if ($analisis_item != null) {
                                $arr[trans('forms.estado')] = $analisis_item->estado['nombre_trans'];
                                $arr[trans('analisis_item.costo_unitario')] = (float) $analisis_item->costo_unitario_adaptado;
                                $arr[trans('analisis_precios.costo_coeficiente_k')] = (float) $analisis_item->costo_unitario_adaptado * $analisis_precios->coeficiente_k;
                            }
                            else {
                                $arr[trans('forms.estado')] = '';
                                $arr[trans('analisis_item.costo_unitario')] = (float) '';
                                $arr[trans('analisis_precios.costo_coeficiente_k')] = (float) '';
                            }
                            return $arr;
                        });

                        $last = count($arr_excel) + 2;
                        $sheet->fromArray($arr_excel, null, 'A1', false, true);

                        foreach (range('E', 'F') as $key => $char) {
                            $sheet->setColumnFormat([$char.'1:'.$char.'9999' => '0.00']);

                            $sheet->cells($char.'1:'.$char.'9999', function ($cells) {
                                $cells->setAlignment('right');
                            });
                        }

                        $sheet->getStyle("G1:G".$last)->getNumberFormat()->setFormatCode('0.0000');

                        $rows = 1;
                        foreach ($arr_excel as $item) {
                            $rows++;
                            if (strlen($item['Código']) == '4') {
                                $sheet->row($rows, function ($row) {
                                    $row->setBackground('#b2b2b2');;
                                });
                            }
                            elseif (strlen($item['Código']) == '7') {
                                $sheet->row($rows, function ($row) {
                                    $row->setBackground('#dddddd');;
                                });
                            }
                            elseif (strlen($item['Código']) == '10') {
                                $sheet->row($rows, function ($row) {
                                    $row->setBackground('#eeeeee');;
                                });
                            }

                        }
                        $sheet->row(1, function ($row) {
                            $row->setBackground('#808080');
                            $row->setFontColor('#ffffff');
                            $row->setFontWeight('bold');
                        });
                    });
              }

          })->store('xlsx', storage_path('excel/exports'));

        return Response::json(array(
          'href' => '/excel/exports/'.trans('forms.analisis_precios').'_'.$contrato->numero_contrato.'.xlsx',
        ));
    }

//#endregion

//#region Analisis de Item

    /**
     * @param  int  $analisis_item_id
     * @param  string  $accion
     */
    public function updateOrStoreAnalisisItem($analisis_item_id, $accion)
    {
        $analisis_item = AnalisisItem::find($analisis_item_id);

        $user = Auth::user();

        if (!in_array($accion, $analisis_item->acciones_posibles)) {
            Log::error(trans('index.error403'), ['User' => $user, 'Intenta' => '$analisis_item-edit']);
            $jsonResponse['message'] = [trans('index.error403')];
            $jsonResponse['permisos'] = true;
            $jsonResponse['status'] = false;
            return response()->json($jsonResponse);
        }

        $errores = array();

        $puedeGuardar = $analisis_item->puedeGuardar($accion);

        if ($puedeGuardar['status']) {
            foreach ($puedeGuardar['errores'] as $keyError => $valueError) {
                $errores = $puedeGuardar['errores'];
            }
        }

        if (sizeof($errores) > 0) {
            $jsonResponse['status'] = false;
            $jsonResponse['errores'] = $errores;
            $jsonResponse['message'] = [];
            $jsonResponse['error_container'] = '#errores-analisis_precios';
            return response()->json($jsonResponse);
        }

        $analisis_item->createInstanciaHistorial('aprobado_obras');

        $jsonResponse['status'] = true;
        $jsonResponse['borrador'] = 0;
        $jsonResponse['message'] = [trans('analisis_precios.mensajes.'.$accion)];

        $jsonResponse['url'] = route('contratos.ver.incompleto',
          ['id' => $analisis_item->analisis_precios->contrato->id, 'accion' => 'analisis_precios']);
        return response()->json($jsonResponse);
    }

    /**
     * @param  int  $analisis_item_id
     */
    public function AnalisisItemEdit($analisis_item_id)
    {
        $analisis_item = AnalisisItem::findOrFail($analisis_item_id);
        $analisis_precios = $analisis_item->analisis_precios;

        if (!$analisis_item->permite_editar) {
            return redirect()->route('contratos.index');
        }

        if (!Auth::user()->puedeVerCausante($analisis_precios->contrato->causante_id)) {
            return redirect()->route('contratos.index');
        }

        $edit = true;
        $redetermina = false;
        $estado_key = $analisis_item->estado['nombre'];
        return view('analisis_precios.analisis_item.createEdit',
          compact('analisis_item', 'edit', 'estado_key', 'redetermina'));
    }

    /**
     * @param  int  $analisis_item_id
     */
    public function AnalisisItemVer($analisis_item_id)
    {
        $analisis_item = AnalisisItem::findOrFail($analisis_item_id);
        $analisis_precios = $analisis_item->analisis_precios;

        if (!Auth::user()->puedeVerCausante($analisis_precios->contrato->causante_id)) {
            return redirect()->route('contratos.index');
        }

        $edit = false;
        $redetermina = false;
        return view('analisis_precios.analisis_item.createEdit', compact('analisis_item', 'edit', 'redetermina'));
    }

    /**
     * @param  string  $accion
     * @param  int  $analisis_item_id
     */
    public function AnalisisItemstoreUpdate($accion, $analisis_item_id)
    {
        $analisis_item = AnalisisItem::find($analisis_item_id);
        $analisis_precios = $analisis_item->analisis_precios;

        if (!$analisis_item->permite_editar) {
            $jsonResponse['status'] = false;
            $jsonResponse['url'] = route('contratos.index');
            $jsonResponse['message'] = [trans('mensajes.error.permisos')];
            Session::flash('error', trans('mensajes.error.permisos'));

            return response()->json($jsonResponse);
        }

        if (!Auth::user()->puedeVerCausante($analisis_precios->contrato->causante_id)) {
            $jsonResponse['status'] = false;
            $jsonResponse['url'] = route('contratos.index');
            $jsonResponse['message'] = [trans('mensajes.error.permisos')];
            Session::flash('error', trans('mensajes.error.permisos'));
            return response()->json($jsonResponse);
        }

        $errores = array();
        $puedeGuardar = $analisis_item->puedeGuardar($accion, true);

        if (!$puedeGuardar['status']) {
            foreach ($puedeGuardar['errores'] as $keyError => $valueError) {
                $errores_validacion = $puedeGuardar['errores'];

                if (count($errores_validacion) > 0) {
                    foreach ($errores_validacion as $keyError => $valueError)
                        $errores[] = $valueError;
                }
            }
        }

        if (sizeof($errores) > 0) {
            $jsonResponse['status'] = false;
            $jsonResponse['errores'] = true;
            $jsonResponse['errores_li'] = $errores;
            $jsonResponse['error_container'] = '.errores-analisis';

            Session::flash('error', trans('analisis_item.mensajes.error.'.$accion));
            $jsonResponse['message'] = [trans('analisis_item.mensajes.error.'.$accion)];

            return response()->json($jsonResponse);
        }

        if (in_array($accion, $analisis_item->acciones_posibles)) {
            $analisis_item->createProximaInstanciaHistorial($accion);
        }

        if ($accion == "aprobar_precios") {
            $no_aprobados = $analisis_precios->analisis_items->filter(function ($analisis) {
                return $analisis->estado['nombre'] != "aprobado";
            });

            if (count($no_aprobados) == 0) {
                $analisis_precios->createProximaInstanciaHistorial($accion);
            }

            $analisis_item->a_redeterminar = 1;
            $analisis_item->save();

            dispatch((new CalculoItemAdenda($analisis_item_id))->onQueue('calculos_variacion'));
        }

        $jsonResponse['status'] = true;
        $jsonResponse['message'] = [trans('analisis_item.mensajes.success.'.$accion)];
        $jsonResponse['url'] = route('contratos.ver.incompleto',
          ['id' => $analisis_precios->contrato->id, 'accion' => 'analisis_precios']);

        return response()->json($jsonResponse);
    }

//#endregion

//#region Componentes de Analisis de Item

    /**
     * @param  int  $categoria_id
     */
    public function createComponente($categoria_id)
    {
        $categoria = CategoriaModelExtended::findWithClase($categoria_id);
        $componente = $categoria->firstNewComponente();
        $contrato = $categoria->analisis_item->contrato;

        if ($categoria->tiene_indice) {
            $indices = $componente->moneda->indices_select()->where('fecha_inicio', '!=', null)->where('fecha_inicio',
                '<=', $this->fechaDeA($contrato->fecha_oferta, 'd/m/Y', 'Y-m-d'))->get();
            
            $indices_a_borrar = ValorIndicePublicado::where('valor','=',"0.00")->get()->unique("tabla_indices_id");
                
            $indices = $indices->filter(function ($indice) use ($indices_a_borrar){    
                return ! $indices_a_borrar->contains('tabla_indices_id',$indice->id);
            });
        }
        $aprobado = false;
        $accion = 'add';
        return view('analisis_precios.modals.add_edit_componente',
          compact('componente', 'categoria', 'indices', 'accion', 'aprobado'));
    }

    /**
     * @param  int  $componente_id
     */
    public function editComponente($componente_id)
    {
        $componente = ComponenteModelExtended::find($componente_id);
        $categoria = $componente->categoria;
        $contrato = $categoria->analisis_item->contrato;

        if ($categoria->tiene_indice) {
            $indices = $componente->moneda->indices_select()->where('fecha_inicio', '!=', null)->where('fecha_inicio',
                '<=', $this->fechaDeA($contrato->fecha_oferta, 'd/m/Y', 'Y-m-d'))->get();
        }

        $estado = $categoria->analisis_item->estado;

        if ($estado['nombre'] == 'aprobado_obras') {
            $aprobado = true;
        }
        else {
            $aprobado = false;
        }

        $accion = 'edit';
        return view('analisis_precios.modals.add_edit_componente',
          compact('componente', 'categoria', 'indices', 'accion', 'aprobado'));
    }

    /**
     * @param  int  $categoria_id
     * @param  \Illuminate\Http\Request  $request
     */
    public function updateOrStoreComponente($categoria_id, Request $request)
    {
        $input = $request->except(['_token']);
        $id = $input['id'];

        $rules = [
          'nombre' => 'required|'.$this->min3max255(),
        ];

        $errores = array();

        $input['costo_total'] = $this->dosDecToDB($input['costo_total']);
        $input['costo_total_adaptado'] = $this->dosDecToDB($input['costo_total_adaptado']);

        $error_tamanio = $this->validarTamanio($input, 'costo_total');
        if (sizeof($error_tamanio) > 0) {
            $errores['costo_total'] = $error_tamanio['costo_total'];
        }

        $categoria = CategoriaModelExtended::findWithClase($categoria_id);

        if ($categoria->tiene_descripcion) {
            $rules['descripcion'] = $this->min3max255();
        }

        if ($categoria->tiene_indice) {
            $rules['indice_id'] = 'required';
        }

        if ($categoria->tiene_cantidad && $input['cantidad'] != null) {
            $input['cantidad'] = $this->dosDecToDB($input['cantidad']);
            $error_tamanio = $this->validarTamanio($input, 'cantidad');
            if (sizeof($error_tamanio) > 0) {
                $errores['cantidad'] = $error_tamanio['cantidad'];
            }
        }

        if ($categoria->tiene_costo_unitario && $input['costo_unitario'] != null) {
            $input['costo_unitario'] = $this->dosDecToDB($input['costo_unitario']);
            $error_tamanio = $this->validarTamanio($input, 'costo_unitario');
            if (sizeof($error_tamanio) > 0) {
                $errores['costo_unitario'] = $error_tamanio['costo_unitario'];
            }
        }

        $validator = Validator::make($input, $rules);

        if ($validator->fails() || sizeof($errores) > 0) {
            $jsonResponse['status'] = false;
            $jsonResponse['errores'] = array_merge($errores, $validator->getMessageBag()->toArray());
            Session::flash('error', trans('mensajes.error.revisar'));
            $jsonResponse['message'] = [trans('mensajes.error.revisar')];
            return response()->json($jsonResponse);
        }

        if ($id != null) {
            $componente = $categoria->firstNewComponente($id);
        }
        else {
            $componente = $categoria->firstNewComponente();
        }

        $componente->nombre = $input['nombre'];
        $componente->costo_total = $input['costo_total'];
        $componente->costo_total_adaptado = $input['costo_total_adaptado'];

        if ($categoria->tiene_descripcion) {
            $componente->descripcion = $input['descripcion'];
        }

        if ($categoria->tiene_indice) {
            $componente->indice_id = $input['indice_id'];
        }

        if ($categoria->tiene_cantidad) {
            $componente->cantidad = $input['cantidad'];
        }

        if ($categoria->tiene_costo_unitario) {
            $componente->costo_unitario = $input['costo_unitario'];
        }

        $componente->save();

        $componente->calcularTotalCategoria();

        $jsonResponse['status'] = true;

        if (isset($input['agregar_otro'])) {
            $jsonResponse['agregar_otro'] = true;
            $jsonResponse['fields']['inputs'] = ['nombre', 'descripcion', 'costo_total', 'costo_total_adaptado'];
            if ($categoria->tiene_indice) {
                $jsonResponse['fields']['inputs'][] = ['indice_id'];
            }

            if ($categoria->tiene_cantidad) {
                $jsonResponse['fields']['inputs'][] = ['cantidad'];
            }

            if ($categoria->tiene_costo_unitario) {
                $jsonResponse['fields']['inputs'][] = ['costo_unitario'];
            }

            $jsonResponse['modal'] = '#itemizadoAddModal';
        }
        else {
            $jsonResponse['close_modal'] = '#modalComponente';
            $position = '#collpapse_'.$categoria_id;
            $jsonResponse['position'] = '#panel_'.$categoria_id;
        }

        $jsonResponse['container'] = '#analisis_container';

        // Variables para recargar vista
        $analisis_item = $categoria->analisis_item;
        $edit = true;
        $estado_key = $analisis_item->estado['nombre'];
        $jsonResponse['html'] = View::make("analisis_precios.analisis_item.createEditContent",
          compact('analisis_item', 'edit', 'estado_key'))->render();

        $jsonResponse['span']['#costo_unitario_analisis'] = $this->toDosDec($analisis_item->costo_unitario_adaptado);
        $jsonResponse['span']['#costo_coeficiente_k'] = $this->toCuatroDec($analisis_item->costo_unitario_adaptado * $analisis_item->analisis_precios->coeficiente_k);

        $dato = trans('analisis_item.componente').' '.trans('index.de').' '.$categoria->nombre;

        if ($id == null) {
            Session::flash('success', $dato.trans('mensajes.success.creado'));
            $jsonResponse['message'] = [$dato.trans('mensajes.success.creado')];
        }

        return response()->json($jsonResponse);
    }

//#endregion

//#region Eliminar Componente

    /**
     * @param  int  $id
     */
    public function preDelete($id)
    {
        $componente = ComponenteModelExtended::find($id);

        if (Auth::user()->cant('analisis_precios-delete')) {
            $jsonResponse['status'] = false;
            $jsonResponse['title'] = trans('index.eliminar').' '.trans('analisis_item.componente');

            $jsonResponse['message'] = [trans('mensajes.error.permisos')];
            return response()->json($jsonResponse);
        }

        $jsonResponse['status'] = true;
        return response()->json($jsonResponse);
    }

    /**
     * @param  int  $id
     */
    public function delete($id)
    {
        $preDelete = $this->preDelete($id)->getData();
        if ($preDelete->status != true) {
            $jsonResponse['status'] = false;
            $jsonResponse['message'] = $preDelete->message;
            return response()->json($jsonResponse);
        }

        $componente = ComponenteModelExtended::find($id);
        $categoria = CategoriaModelExtended::findWithClase($componente->categoria_id);

        try {
            $componente->delete();
            $categoria->calcularTotal();
        } catch (QueryException $e) {
            Log::error('QueryException', ['Exception' => $e]);
            $jsonResponse['status'] = false;
            $jsonResponse['message'] = [trans('mensajes.error.insert_db')];
            return response()->json($jsonResponse);
        }

        $jsonResponse['status'] = true;
        $jsonResponse['refresh'] = true;
        $jsonResponse['message'] = [trans('mensajes.dato.componente').trans('mensajes.success.eliminado')];

        return response()->json($jsonResponse);
    }

//#endregion

//#region Rendimiento

    /**
     * @param  int  $categoria_id
     */
    public function editRendimiento($categoria_id)
    {
        $categoria = CategoriaModelExtended::findWithClase($categoria_id);
        $rendimiento = $categoria->rendimiento;
        if ($rendimiento == null) {
            $rendimiento = new Rendimiento();
            $rendimiento->valor = 1;
        }

        $unidades = Unidad::getOpciones();

        return view('analisis_precios.modals.edit_rendimiento', compact('categoria_id', 'rendimiento', 'unidades'));
    }

    /**
     * @param  int  $categoria_id
     * @param  \Illuminate\Http\Request  $request
     */
    public function updateRendimiento($categoria_id, Request $request)
    {
        $categoria = CategoriaModelExtended::findWithClase($categoria_id);
        $analisis_precios = $categoria->analisis_item->analisis_precios;

        if (!$analisis_precios->permite_editar) {
            $jsonResponse['status'] = false;
            $jsonResponse['url'] = route('contratos.index');
            $jsonResponse['message'] = [trans('mensajes.error.permisos')];
            Session::flash('error', trans('mensajes.error.permisos'));
            return response()->json($jsonResponse);
        }

        if (!$categoria->analisis_item->item->is_unidad_medida) {
            $jsonResponse['status'] = false;
            $jsonResponse['url'] = route('contratos.index');
            $jsonResponse['message'] = [trans('mensajes.error.permisos')];
            Session::flash('error', trans('mensajes.error.permisos'));
            return response()->json($jsonResponse);
        }

        if (!Auth::user()->puedeVerCausante($analisis_precios->contrato->causante_id)) {
            $jsonResponse['status'] = false;
            $jsonResponse['url'] = route('contratos.index');
            $jsonResponse['message'] = [trans('mensajes.error.permisos')];
            Session::flash('error', trans('mensajes.error.permisos'));
            return response()->json($jsonResponse);
        }

        $input = $request->except(['_token']);

        $input['rendimiento'] = $this->dosDecToDB($input['rendimiento']);
        $inputVar = explode(".", $input['rendimiento']);

        $errores = array();
        if (!isset($inputVar[1])) {
            $inputVar[1] = "00";
        }

        if (strlen($inputVar[0]) > 6) {
            $errores['rendimiento'] = trans('mensajes.error.max_number_rendimiento');
        }

        if ($inputVar[0] < 1) {
            $errores['rendimiento'] = trans('mensajes.error.min_number_rendimiento');
        }

        if (strlen($inputVar[1]) > 2) {
            $errores['rendimiento'] = trans('mensajes.error.max_decimal_2');
        }

        if (sizeof($errores) > 0) {
            $jsonResponse['status'] = false;
            $jsonResponse['errores'] = $errores;
            Session::flash('error', trans('mensajes.error.revisar'));
            $jsonResponse['message'] = [trans('mensajes.error.revisar')];
            return response()->json($jsonResponse);
        }

        $rendimiento = Rendimiento::FindOrNew($input['id']);
        $rendimiento->categoria_id = $categoria_id;
        $rendimiento->valor = $input['rendimiento'];
        $rendimiento->unidad_id = $input['unidad_id'];
        $rendimiento->save();

        $categoria->calcularTotal();

        $jsonResponse['status'] = true;
        $jsonResponse['close_modal'] = '#modalRendimiento';

        $jsonResponse['container'] = '#analisis_container';

        // Variables para recargar vista
        $analisis_item = $categoria->analisis_item;
        $edit = true;
        $estado_key = $analisis_item->estado['nombre'];
        $jsonResponse['html'] = View::make("analisis_precios.analisis_item.createEditContent",
          compact('analisis_item', 'edit', 'estado_key'))->render();
        $jsonResponse['span']['#costo_unitario_analisis'] = $this->toDosDec($analisis_item->costo_unitario_adaptado);
        $jsonResponse['span']['#costo_coeficiente_k'] = $this->toCuatroDec($analisis_item->costo_unitario_adaptado * $analisis_item->analisis_precios->coeficiente_k);

        $jsonResponse['message'] = [trans('analisis_item.rendimiento').trans('mensajes.success.editado')];

        return response()->json($jsonResponse);
    }

//#endregion

    /**
     * @param  int  $clase_id
     * @param  string  $seccion
     */
    public function historial($clase_id, $seccion)
    {
        if ($seccion == 'analisis_precios') {
            $instancias = AnalisisPrecios::find($clase_id)->instancias;
        }
        elseif ($seccion == 'analisis_item') {
            $instancias = AnalisisItem::find($clase_id)->instancias;
        }

        $jsonResponse['view'] = View::make('contratos.contratos.historial', compact('instancias', 'seccion'))->render();
        $jsonResponse['title'] = trans('index.de').' '.trans('contratos.'.$seccion);

        return response()->json($jsonResponse);
    }
}
