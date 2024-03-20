<?php

namespace App\Http\Controllers\Contratos;

use Contrato\Ampliacion\Ampliacion;
use Contrato\Contrato;
use Cronograma\Cronograma;
use Cronograma\ItemCronograma;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Itemizado\Item;
use Itemizado\Itemizado;
use Log;
use Maatwebsite\Excel\Facades\Excel;
use Response;
use View;

class CronogramaController extends ContratosControllerExtended
{

    public function __construct()
    {
        View::share('ayuda', 'contrato');
        $this->middleware('auth', ['except' => 'logout']);
    }

//#region Guardar Cronograma

    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $contrato_id
     */
    public function updateOrStore(Request $request, $contrato_id)
    {
        $user = Auth::user();
        $contrato = Contrato::find($contrato_id);

        if ($user->cant('cronograma-edit')) {
            Log::error(trans('index.error403'), ['User' => $user, 'Intenta' => 'cronograma-edit']);

            $jsonResponse['message'] = [trans('index.error403')];
            $jsonResponse['permisos'] = true;
            $jsonResponse['status'] = false;

            return response()->json($jsonResponse);
        }

        if (!Auth::user()->puedeVerCausante($contrato->causante_id)) {
            Log::error(trans('mensajes.error.no_pertenece_causante'), ['Usuario' => Auth::user()->id]);

            Session::flash('error', trans('mensajes.error.no_pertenece_causante'));
            $jsonResponse['status'] = false;
            $jsonResponse['message'] = trans('mensajes.error.no_pertenece_causante');

            return response()->json($jsonResponse);
        }

        $jsonResponse = $this->updateCronograma($request->except(['_token', 'scroll_ayuda', 'js_applied']), $contrato);
        $jsonResponse = $jsonResponse->getData();

        return response()->json($jsonResponse);
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     */
    public function updateOrStoreAmpliacion(Request $request, $id)
    {
        $user = Auth::user();
        $ampliacion = Ampliacion::find($id);

        if ($user->cant('cronograma-edit')) {
            Log::error(trans('index.error403'), ['User' => $user, 'Intenta' => 'cronograma-edit']);

            $jsonResponse['message'] = [trans('index.error403')];
            $jsonResponse['permisos'] = true;
            $jsonResponse['status'] = false;

            return response()->json($jsonResponse);
        }

        if (!Auth::user()->puedeVerCausante($ampliacion->contrato->causante_id)) {
            Log::error(trans('mensajes.error.no_pertenece_causante'), ['Usuario' => Auth::user()->id]);
            Session::flash('error', trans('mensajes.error.no_pertenece_causante'));

            $jsonResponse['status'] = false;
            $jsonResponse['message'] = trans('mensajes.error.no_pertenece_causante');

            return response()->json($jsonResponse);
        }

        $jsonResponse = $this->updateCronograma(
          $request->except(['_token', 'scroll_ayuda', 'js_applied']),
          $ampliacion
        );

        $jsonResponse = $jsonResponse->getData();
        return response()->json($jsonResponse);
    }

    /**
     * @param  array  $input
     * @param  Contrato\Contrato| Contrato\Ampliacion\Ampliacion  $object
     */
    public function updateCronograma($input, $object)
    {
        // Validaciones Custom
        $errores = [];

        foreach ($object->cronogramas as $cronograma) {
            $meses = Cronograma::find($cronograma->id)->meses;

            foreach ($cronograma->items_hoja as $valueItem) {
                $item = Item::find($valueItem->id);

                if ($item->is_unidad_medida)
                    $total = $item->cantidad;
                else
                    $total = 100;

                $totalSuma = 0;
                $falta_uno = false;

                for ($mes = 1; $mes <= $meses; $mes++) {
                    $itemCronograma = ItemCronograma::where([
                      'mes' => $mes,
                      'item_id' => $valueItem->id,
                      'cronograma_id' => $cronograma->id,
                    ])->first();

                    if ($itemCronograma == null) {
                        $errores[$valueItem->mes][] = trans('validation_custom.cronograma.sin_cargar', [
                          'moneda' => $cronograma->contrato_moneda->moneda->nombre,
                          'item' => $valueItem->descripcion_codigo
                        ]);

                        $falta_uno = true;

                        break 1;
                    }

                    if (!$falta_uno) {
                        if ($item->is_unidad_medida)
                            $totalSuma += $itemCronograma->cantidad;
                        else
                            $totalSuma += $itemCronograma->porcentaje;
                    }
                }

                if (!$falta_uno && (abs($total - $totalSuma) > config('custom.delta'))) {
                    $um = '';

                    if ($item->is_unidad_medida)
                        $um = $item->unidad_medida_nombre;

                    $errores[$valueItem->mes][] = trans('validation_custom.cronograma.suma', [
                        'moneda' => $cronograma->contrato_moneda->moneda->nombre,
                        'item' => $valueItem->descripcion_codigo,
                        'resultado' => $totalSuma.$um,
                        'esperado' => $total.$um
                      ]);
                }
            }
        }

        if (sizeof($errores) > 0) {
            $jsonResponse['status'] = false;
            $jsonResponse['errores'] = $errores;
            $jsonResponse['message'] = [];
            $jsonResponse['error_container'] = '#errores-cronograma';

            return response()->json($jsonResponse);
        }

        $object->load(['causante']);
        $causante = $object->causante;

        $dobleFirma = false;
        $firmaAr = null;
        $firmaPy = null;

        if ($causante) {
            $dobleFirma = $causante->doble_firma;

            if ($dobleFirma && $causante->jefe_obras_ar == Auth::user()->id)
                $firmaAr = Auth::user()->id;

            if ($dobleFirma && $causante->jefe_obras_py == Auth::user()->id)
                $firmaPy = Auth::user()->id;
        }

        foreach ($object->cronogramas as $cronograma) {
            $cronograma->borrador = 0;

            if ($dobleFirma) {
                // Firma del lado argentino
                if ($firmaAr)
                    $cronograma->firma_ar = $firmaAr;
                else
                    $firmaAr = $cronograma->firma_ar;

                // Firma del lado paraguayo
                if ($firmaPy)
                    $cronograma->firma_py = $firmaPy;
                else
                    $firmaPy = $cronograma->firma_py;

                $dobleFirma = $cronograma->doble_firma = !$firmaAr || !$firmaPy;
            }

            $cronograma->save();
        }

        if (!$dobleFirma) {
            $this->completarContrato($object->id, $object->getClassName());

            $this->createInstanciaHistorial($object, 'cronograma', 'aprobado');
        }
        else if ($firmaAr || $firmaPy)
            $this->createInstanciaHistorial($object, 'cronograma', 'firma');
        else
            $this->createInstanciaHistorial($object, 'cronograma', 'a_firmar');

        $jsonResponse['status'] = true;

        if ($object->getShortClassName() == 'Ampliacion')
            $jsonResponse['url'] = route('contratos.ver', ['id' => $object->contrato_id]);
        else
            $jsonResponse['url'] = route('contratos.ver', ['id' => $object->id]);

        $message = trans('mensajes.dato.cronograma') . trans('mensajes.success.guardado');

        Session::flash('success', $message);
        $jsonResponse['message'] = [$message];

        return response()->json($jsonResponse);
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $contrato_id
     */
    public function firmar(Request $request, $contrato_id)
    {
        $user = Auth::user();
        $contrato = Contrato::find($contrato_id);

        if ($user->cant('cronograma-edit')) {
            Log::error(trans('index.error403'), ['User' => $user, 'Intenta' => 'cronograma-edit']);

            $jsonResponse['message'] = [trans('index.error403')];
            $jsonResponse['permisos'] = true;
            $jsonResponse['status'] = false;

            return response()->json($jsonResponse);
        }

        if (!Auth::user()->puedeVerCausante($contrato->causante_id)) {
            Log::error(trans('mensajes.error.no_pertenece_causante'), ['Usuario' => Auth::user()->id]);

            Session::flash('error', trans('mensajes.error.no_pertenece_causante'));
            $jsonResponse['status'] = false;
            $jsonResponse['message'] = trans('mensajes.error.no_pertenece_causante');

            return response()->json($jsonResponse);
        }

        return $this->firmarCronograma($request->except(['_token', 'scroll_ayuda', 'js_applied']), $contrato);
    }

    /**
     * @param $contrato_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function firmarCronograma($input, $object) {
        $user = Auth::user();

        $object->load(['causante']);
        $causante = $object->causante;

        $dobleFirma = false;
        $firmaAr = null;
        $firmaPy = null;

        if ($causante) {
            $dobleFirma = $causante->doble_firma;

            if ($dobleFirma && $causante->jefe_obras_ar == $user->id)
                $firmaAr = $user->id;

            if ($dobleFirma && $causante->jefe_obras_py == $user->id)
                $firmaPy = $user->id;
        }

        foreach ($object->cronogramas as $cronograma) {
            $cronograma->borrador = 0;

            // Firma del lado argentino
            if ($firmaAr)
                $cronograma->firma_ar = $firmaAr;
            else
                $firmaAr = $cronograma->firma_ar;

            // Firma del lado paraguayo
            if ($firmaPy)
                $cronograma->firma_py = $firmaPy;
            else
                $firmaPy = $cronograma->firma_py;

            $dobleFirma = $cronograma->doble_firma = !$firmaAr || !$firmaPy;

            $cronograma->save();
        }

        if (!$dobleFirma) {
            $object_id = $object->id;

            $this->completarContrato($object_id, $object->getClassName());

            $this->createInstanciaHistorial($object, 'cronograma', 'aprobado');
        }
        else if ($firmaAr || $firmaPy)
            $this->createInstanciaHistorial($object, 'cronograma', 'firma');
        else
            $this->createInstanciaHistorial($object, 'cronograma', 'a_firmar');

        $jsonResponse['status'] = true;

        if ($object->getShortClassName() == 'Ampliacion')
            $jsonResponse['url'] = route('contratos.ver', ['id' => $object->contrato_id]);
        else
            $jsonResponse['url'] = route('contratos.ver', ['id' => $object->id]);

        $message = trans('mensajes.success.firmado', [
          'type' =>  trans('mensajes.dato.cronograma'),
          'name' => ''
        ]);

        Session::flash('success', $message);
        $jsonResponse['message'] = [$message];

        return response()->json($jsonResponse);
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $contrato_id
     */
    public function borrador(Request $request, $contrato_id)
    {
        $user = Auth::user();
        $contrato = Contrato::find($contrato_id);

        if ($user->cant('cronograma-edit')) {
            Log::error(trans('index.error403'), ['User' => $user, 'Intenta' => 'cronograma-edit']);

            $jsonResponse['message'] = [trans('index.error403')];
            $jsonResponse['permisos'] = true;
            $jsonResponse['status'] = false;

            return response()->json($jsonResponse);
        }

        if (!Auth::user()->puedeVerCausante($contrato->causante_id)) {
            Log::error(trans('mensajes.error.no_pertenece_causante'), ['Usuario' => Auth::user()->id]);

            Session::flash('error', trans('mensajes.error.no_pertenece_causante'));
            $jsonResponse['status'] = false;
            $jsonResponse['message'] = trans('mensajes.error.no_pertenece_causante');

            return response()->json($jsonResponse);
        }

        return $this->borradorCronograma($request->except(['_token', 'scroll_ayuda', 'js_applied']), $contrato);
    }

    /**
     * @param $contrato_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function borradorCronograma($input, $object) {
        $user = Auth::user();

        $object->load(['causante']);
        $causante = $object->causante;

        // Verificar causante
        if (!$causante) {
            $jsonResponse['status'] = false;
            $jsonResponse['errores'] = [trans('mensajes.error.doble_firma_sin_causante', [
              'name' => trans('index.contrato')
            ])];
            $jsonResponse['message'] = [];
            $jsonResponse['error_container'] = '#errores-itemizado';

            return response()->json($jsonResponse);
        }

        // Verifica que si adminte doble firma
        if (!$causante->doble_firma) {
            return $this->errorJsonResponse([trans('mensajes.error.doble_firma_no_admite', [
              'name' => trans('index.contrato')
            ])]);
        }

        if ($causante->jefe_contrato_ar != $user->id && $causante->jefe_contrato_py != $user->id) {
            return $this->errorJsonResponse([trans('mensajes.error.borrador_denegado', [
              'name' => trans('index.contrato')
            ])]);
        }

        if ($causante) {
            $dobleFirma = $causante->doble_firma;

            if ($dobleFirma && $causante->jefe_obras_ar == $user->id)
                $firmaAr = $user->id;

            if ($dobleFirma && $causante->jefe_obras_py == $user->id)
                $firmaPy = $user->id;
        }

        foreach ($object->cronogramas as $cronograma) {
            $cronograma->borrador = 1;
            $cronograma->firma_ar = null;
            $cronograma->firma_py = null;
            $cronograma->doble_firma = null;
            $cronograma->save();
        }

        if ($object->getShortClassName() == 'Ampliacion')
            $jsonResponse['url'] = route('contratos.ver', ['id' => $object->contrato_id]);
        else
            $jsonResponse['url'] = route('contratos.ver', ['id' => $object->id]);

        $message = trans('mensajes.success.borrador', [
          'type' => trans('mensajes.dato.cronograma'),
          'name' => ''
        ]);

        Session::flash('success', $message);
        $jsonResponse['message'] = [$message];

        return response()->json($jsonResponse);
    }

//#endregion

//#region Guardar ItemCronograma
    /**
     * @param  int  $cronograma_id
     * @param  int  $item_id
     */
    public function getHtmlEdit($cronograma_id, $item_id)
    {
        $meses = Cronograma::find($cronograma_id)->meses;
        $item = Item::select('id', 'categoria_id', 'descripcion', 'cantidad', 'item_original_id',
          'certificado')->find($item_id);

        if ($item->is_ajuste_alzado) {
            $mostrar = 'porcentaje';
            $total_item = 100;
        }
        else {
            $mostrar = 'cantidad';
            $total_item = $item->cantidad;
        }

        $itemsCronograma = collect();
        $total = 0;
        for ($mes = 1; $mes <= $meses; $mes++) {
            $itemCronograma = ItemCronograma::firstOrNew([
              'mes' => $mes, 'item_id' => $item_id, 'cronograma_id' => $cronograma_id,
            ]);

            if ($itemCronograma->$mostrar == null) {
                $itemCronograma->$mostrar = 0;
            }

            $total += $itemCronograma->$mostrar;
            $itemsCronograma->push($itemCronograma);
        }

        $faltante = $total_item - $total;

        setlocale(LC_MONETARY, 'it_IT');
        $total = number_format((float) $total, 2, ',', '.');
        $total_item = number_format((float) $total_item, 2, ',', '.');
        $faltante = number_format((float) $faltante, 2, ',', '.');

        $contrato_o_adenda = $itemCronograma->cronograma->contrato;
        if ($contrato_o_adenda->is_ampliacion) {
            $contrato = $contrato_o_adenda->contrato;
        }
        else {
            $contrato = $contrato_o_adenda->contrato_original;
        }

        $certificados = $contrato->certificados;
        if (count($certificados) == 0) {
            $mes_proximo_certificado = 0;
        }
        else {
            $mes_proximo_certificado = $contrato->certificados->max('mes') + 1;
        }

        return view('contratos.contratos.show.cronograma.modals.add_edit_item',
          compact('cronograma_id', 'item', 'itemsCronograma', 'total', 'total_item', 'faltante',
            'mes_proximo_certificado'));
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $cronograma_id
     * @param  int  $contrato_id
     */
    public function updateItemCronograma(Request $request, $cronograma_id, $item_id)
    {
        $input = $request->except(['_token', 'scroll_ayuda', 'js_applied']);
        // Validaciones Custom
        $errores = array();

        $date = date("Y-m-d H:i:s");
        $valores = array();

        $item = Item::select('id', 'categoria_id', 'subtotal', 'cantidad', 'monto_unitario', 'item_original_id',
          'certificado')->find($item_id);

        $cronograma = Cronograma::find($cronograma_id);
        $contrato_o_adenda = $cronograma->contrato;

        if ($contrato_o_adenda->is_ampliacion) {
            $contrato = $contrato_o_adenda->contrato;
        }
        else {
            $contrato = $contrato_o_adenda->contrato_original;
        }

        $certificados = $contrato->certificados;
        if (count($certificados) == 0) {
            $mes_proximo_certificado = 0;
        }
        else {
            $mes_proximo_certificado = $contrato->certificados->max('mes') + 1;
        }

        foreach ($input['valor'] as $keyMes => $valueValor) {
            if ($keyMes >= $mes_proximo_certificado) {
                if ($valueValor == null) {
                    $input['valor'][$keyMes] = 0;
                }
                else {
                    $input['valor'][$keyMes] = str_replace(".", "", $input['valor'][$keyMes]);
                    $input['valor'][$keyMes] = str_replace(",", ".", $input['valor'][$keyMes]);
                }

                if ((float) $input['valor'][$keyMes] < 0) {
                    $errores['valor_'.$keyMes] = trans('validation_custom.menor_0',
                      ['attribute' => trans('forms.valor')]);
                }

                $inputVar = explode(".", $input['valor'][$keyMes]);

                if (!isset($inputVar[1])) {
                    $inputVar[1] = "00";
                }

                if (strlen($inputVar[1]) < 2) {
                    $inputVar[1] = str_pad($inputVar[1], 2, "0");
                }

                if (strlen($inputVar[0]) > 12) {
                    $errores['valor_'.$keyMes] = trans('mensajes.error.max_number_12');
                }

                if (strlen($inputVar[1]) > 2) {
                    $errores['valor_'.$keyMes] = trans('mensajes.error.max_decimal_2');
                }

                $valor = 0;
                $cantidad = 0;
                $porcentaje = 0;

                // En ambos casos calculo valor
                if ($item->is_unidad_medida) {
                    // Si es unidad de medida guardo cantidad y calculo porcentaje
                    $cantidad = $input['valor'][$keyMes];
                    $porcentaje = round((($input['valor'][$keyMes] / $item->cantidad) * 100), 4);
                    $valor = $cantidad * $item->monto_unitario;
                }
                else {
                    // Si es ajuste alzado guardo porcentaje
                    $porcentaje = round($input['valor'][$keyMes], 4);
                    $valor = ($item->subtotal / 100) * $input['valor'][$keyMes];
                }

                $valores[] = ([
                  'mes' => $keyMes, 'valor' => $valor, 'cantidad' => $cantidad, 'porcentaje' => $porcentaje,
                  'item_id' => $item_id, 'cronograma_id' => $cronograma_id, 'user_creator_id' => Auth::user()->id,
                  'user_modifier_id' => Auth::user()->id, 'updated_at' => $date, 'created_at' => $date,
                ]);
            }
        }

        if (sizeof($errores) > 0) {
            $jsonResponse['status'] = false;
            $jsonResponse['errores'] = $errores;
            $jsonResponse['message'] = [];
            return response()->json($jsonResponse);
        }

        if (sizeof($valores) > 0) {
            foreach ($valores as $keyMes => $valueValor) {
                $item_cronograma = ItemCronograma::whereMes($valueValor['mes'])->whereItemId($item_id)->whereCronogramaId($cronograma_id)->first();

                if ($item_cronograma != null) {

                    $item_cronograma->valor = $valueValor['valor'];
                    $item_cronograma->cantidad = $valueValor['cantidad'];
                    $item_cronograma->porcentaje = $valueValor['porcentaje'];

                    $item_cronograma->updated_at = date("Y-m-d H:i:s");
                    $item_cronograma->user_modifier_id = Auth::user()->id;
                    $item_cronograma->save();

                    unset($valores[$keyMes]);

                    if (!isset($contrato_id)) {
                        $clase_type = $item_cronograma->cronograma->clase_type;
                        $contrato_id = $item_cronograma->cronograma->contrato_id;
                    }
                }
            }
            $meses = $valueValor['mes'];
        }

        // No se puede cargar valores en meses ya certificados
        if (!$item->certificado && $mes_proximo_certificado > 1) {
            for ($mes = 1; $mes < $mes_proximo_certificado; $mes++) {
                $valores[] = ([
                  'mes' => $mes, 'valor' => 0, 'cantidad' => 0, 'porcentaje' => 0, 'item_id' => $item_id,
                  'cronograma_id' => $cronograma_id, 'user_creator_id' => Auth::user()->id,
                  'user_modifier_id' => Auth::user()->id, 'updated_at' => $date, 'created_at' => $date,
                ]);
            }
            $item->certificado = 1;
            $item->save();
        }

        if ($item->is_unidad_medida && $item->certificado) {
            for ($mes = 1; $mes < $mes_proximo_certificado; $mes++) {
                $item_cronograma = ItemCronograma::whereMes($mes)->whereItemId($item_id)->whereCronogramaId($cronograma_id)->first();
                if ($item_cronograma != null) {
                    $porcentaje = round((($item_cronograma->cantidad / $item->cantidad) * 100), 4);
                    $item_cronograma->porcentaje = $porcentaje;
                    $item_cronograma->save();
                }
            }
        }

        if (sizeof($valores) > 0) {
            try {
                ItemCronograma::insert($valores);
            }
            catch (\QueryException $e) {
                Log::error('QueryException', ['Exception' => $e]);
                Session::flash('error', trans('mensajes.error.insert_db'));
                $jsonResponse['status'] = false;
                $jsonResponse['message'] = [trans('mensajes.error.insert_db')];
                return response()->json($jsonResponse);
            }

            if (!isset($contrato_id)) {
                $item_cronograma = ItemCronograma::whereCronogramaId($cronograma_id)->first();
                $contrato_id = $item_cronograma->cronograma->contrato_id;
                $clase_type = $item_cronograma->cronograma->clase_type;
            }
        }

        $this->updateParents($cronograma, $item_id, $meses);

        $jsonResponse['status'] = true;
        $jsonResponse['close_modal'] = '#ModalItemCronograma';

        $jsonResponse['container'] = '#cronograma_container';

        if ($clase_type == 'Contrato\Contrato') {
            $jsonResponse['html'] = app('ContratosController')->getViews($contrato_id, 'cronograma')->getData()->view;
        }
        elseif ($clase_type == 'Contrato\Ampliacion\Ampliacion') {
            $jsonResponse['html'] = app('AmpliacionController')->getViewsCronograma($contrato_id,
              'cronograma')->getData()->view;
        }

        $jsonResponse['message'] = [trans('mensajes.dato.item_cronograma').trans('mensajes.success.editados')];

        return response()->json($jsonResponse);
    }

    /**
     * @param  Cronograma\Cronograma  $cronograma
     * @param  int  $item_id
     * @param  int  $meses
     */
    public function updateParents($cronograma, $item_id, $meses, $suma_hijos = [])
    {
        if ($item_id == null) {
            return 'OK';
        }

        $item = Item::find($item_id);
        $cronograma_id = $cronograma->id;

        if (sizeof($item->allChilds) > 0) {
            $date = date("Y-m-d H:i:s");
            foreach ($item->allChilds as $keySubItem => $valueSubItem) {

                for ($mes = 1; $mes <= $meses; $mes++) {
                    if (!isset($suma_hijos[$mes])) {
                        $suma_hijos[$mes] = ([
                          'mes' => $mes, 'valor' => 0, 'cantidad' => 0, 'porcentaje' => 0, 'item_id' => $item_id,
                          'cronograma_id' => $cronograma_id, 'user_creator_id' => Auth::user()->id,
                          'user_modifier_id' => Auth::user()->id, 'updated_at' => $date, 'created_at' => $date,
                        ]);
                    }

                    $item_cronograma_temp = ItemCronograma::select('valor', 'cantidad',
                      'porcentaje')->whereItemId($valueSubItem->id)->whereCronogramaId($cronograma_id)->whereMes($mes)->first();

                    if ($item_cronograma_temp == null) {
                        $valor = 0;
                        $cantidad = 0;
                    }
                    else {
                        $valor = $item_cronograma_temp->valor;
                        $cantidad = $item_cronograma_temp->cantidad;
                    }

                    $suma_hijos[$mes]['valor'] = $suma_hijos[$mes]['valor'] + $valor;
                    $suma_hijos[$mes]['cantidad'] = $suma_hijos[$mes]['cantidad'] + $cantidad;
                    $suma_hijos[$mes]['porcentaje'] = round(($suma_hijos[$mes]['valor'] / $item->subtotal) * 100, 4);
                }
            }

            if (sizeof($suma_hijos) > 0) {
                foreach ($suma_hijos as $keyValor => $valueValor) {
                    $item_cronograma = ItemCronograma::whereMes($valueValor['mes'])->whereItemId($item_id)->whereCronogramaId($cronograma_id)->first();

                    $valueValor['valor'] = number_format(round($valueValor['valor'], 2), 2, '.', '');
                    if ($item_cronograma != null) {

                        $item_cronograma->user_modifier_id = Auth::user()->id;
                        $item_cronograma->valor = $valueValor['valor'];
                        $item_cronograma->porcentaje = $valueValor['porcentaje'];
                        $item_cronograma->cantidad = $valueValor['cantidad'];
                        $item_cronograma->save();
                        unset($suma_hijos[$keyValor]);
                        if (!isset($contrato_id)) {
                            $contrato_id = $item_cronograma->cronograma->contrato_id;
                        }
                    }
                }
                $meses = $valueValor['mes'];

                ItemCronograma::insert($suma_hijos);
            }
        }

        $this->updateParents($cronograma, $item->padre_id, $meses);
        return 'OK';
    }

//#endregion

    /**
     * @param  \Illuminate\Http\Request  $request
     */
    public function exportar(Request $request)
    {
        $input = $request->all();
        $contrato = Contrato::where('id', $input['excel_input'])->first();
        Excel::create(trans('index.cronogramas').'_'.$contrato->numero_contrato,
          function ($excel) use ($input, $contrato) {

              $visualizacion = $input['visualizacion'];

              foreach ($contrato->contratos_monedas as $valueContratoMoneda) {
                  $excel->sheet($valueContratoMoneda->moneda->nombre_simbolo,
                    function ($sheet) use ($valueContratoMoneda, $visualizacion, $input, $contrato) {

                        if ($input['version'] == 'vigente' && $contrato->has_itemizado_vigente) {
                            $itemizado = Itemizado::where('id', $valueContratoMoneda->itemizado_vigente_id)->first();
                            $cronograma = Cronograma::where('itemizado_id', $itemizado->id)->first();
                        }
                        else {
                            $itemizado = $valueContratoMoneda->itemizado;
                            $cronograma = $valueContratoMoneda->itemizado->cronograma;
                        }


                        $items = Item::where('itemizado_id', $itemizado->id)->orderBy('codigo', 'ASC')->get();

                        $totales = [trans('forms.total')];

                        foreach ($items as $k => $item) {

                            $arr_excel[$k] = [
                              trans('forms.codigo') => $item->codigo,
                              trans('forms.item') => $item->item ? $item->item : $item->codigo,
                              trans('forms.descripcion') => $item->descripcion,
                            ];

                            $itemCronogramas = ItemCronograma::where('item_id', $item->id)->get();

                            $mes = 0;
                            foreach ($itemCronogramas as $itemCro) {

                                if ($visualizacion == 'porcentaje') {
                                    $arr_excel[$k][$itemCro->mes] = number_format((float) round($itemCro->porcentaje,
                                        2), 2, ',', '.').' '.'%'.'   ';

                                    $mes++;
                                    if (isset($totales[$mes])) {
                                        $totales[$mes] = $cronograma->valorItemizado($visualizacion, $mes);
                                    }
                                    else {
                                        $totales[$mes] = $cronograma->valorItemizado($visualizacion, $mes);
                                    }
                                }
                                elseif ($visualizacion == 'moneda') {
                                    if ($item->is_hoja) {
                                        if (!$itemCro->valor or $itemCro->valor == 0) {
                                            $arr_excel[$k][$itemCro->mes] = number_format((float) 0, 2, ',', '.');
                                        }
                                        else {
                                            $arr_excel[$k][$itemCro->mes] = (float) $itemCro->valor;
                                        }
                                    }
                                    else {
                                        if (!$itemCro->valor) {
                                            $arr_excel[$k][$itemCro->mes] = number_format((float) 0, 2, ',', '.');
                                        }
                                        else {
                                            $arr_excel[$k][$itemCro->mes] = (float) $itemCro->valor;
                                        }
                                    }

                                    if ($item->is_hoja) {
                                        if (!$itemCro->valor) {
                                            $arr_excel[$k][$itemCro->mes] = number_format((float) 0, 2, ',', '.');
                                        }
                                        else {
                                            $arr_excel[$k][$itemCro->mes] = (float) $itemCro->valor;
                                        }

                                        if (isset($totales[$itemCro->mes])) {
                                            $totales[$itemCro->mes] += (float) $itemCro->valor;
                                        }
                                        else {
                                            $totales[$itemCro->mes] = (float) $itemCro->valor;
                                        }
                                    }
                                }
                                elseif ($visualizacion == 'all') {
                                    $mes++;

                                    if ($itemCro->cantidad == 0) {
                                        if (!$item->is_hoja) {
                                            $arr_excel[$k][$itemCro->mes] = number_format((float) round($itemCro->porcentaje,
                                                2), 2, ',', '.').'%'.'   ';
                                        }
                                        else {
                                            $arr_excel[$k][$itemCro->mes] = $cronograma->valorItem($item->id,
                                                $visualizacion, $mes).'   ';
                                        }
                                    }
                                    else {
                                        if (!$item->is_hoja) {
                                            $arr_excel[$k][$itemCro->mes] = $cronograma->valorItem($item->id,
                                                $visualizacion, $mes).'   ';
                                        }
                                        else {
                                            $arr_excel[$k][$itemCro->mes] = (float) $itemCro->cantidad.' '.$item->unidad_medida_nombre.'   ';
                                        }
                                    }

                                    if (isset($totales[$mes])) {
                                        $totales[$mes] = $cronograma->valorItemizado($visualizacion, $mes);
                                    }
                                    else {
                                        $totales[$mes] = $cronograma->valorItemizado($visualizacion, $mes);
                                    }
                                }
                            }
                        }

                        array_unshift($totales, ' ');
                        $arr_excel[] = $totales;
                        $last = count($arr_excel) + 1;
                        $mesLength = count($arr_excel[0]) - 1;

                        $sheet->fromArray($arr_excel, null, 'A1', false, true);

                        $rows = 1;
                        foreach ($arr_excel as $item) {
                            if (isset($item['Código'])) {
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
                        }
                        $alphabet = range('A', 'Z');

                        $limit = $alphabet[$mesLength];

                        foreach (range('C', $limit) as $key => $char) {
                            $sheet->setColumnFormat([$char.'2:'.$char.'9999' => '0.00']);

                            $sheet->cells($char.'2:'.$char.'9999', function ($cells) {
                                $cells->setAlignment('right');
                            });
                        }

                        $sheet->row(1, function ($row) {
                            $row->setBackground('#808080');
                            $row->setFontColor('#ffffff');
                            $row->setFontWeight('bold');
                        });

                        $sheet->row($last, function ($row) {
                            $row->setBackground('#808080');
                            $row->setFontColor('#ffffff');
                            $row->setFontWeight('bold');
                        });
                    });
              }
          })->store('xlsx', storage_path('excel/exports'));

        return Response::json(array(
          'href' => '/excel/exports/'.trans('index.cronogramas').'_'.$contrato->numero_contrato.'.xlsx',
        ));
    }

}
