<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Indice\Clasificacion;
use Indice\ConponenteIndiceCompuesto;
use Indice\Fuente;
use Indice\IndiceAPublicar;
use Indice\IndiceTabla1;
use Indice\InstanciaPublicacionIndice;
use Indice\PublicacionIndice;
use Indice\ValorIndice;
use Indice\ValorIndicePublicado;
use Log;
use View;
use Yacyreta\Moneda;

class IndicesController extends Controller
{

    /**
     * @param  int $publicacion_id
     */
    public function create($publicacion_id)
    {
        $clasificaciones = Clasificacion::distinct('categoria')->get();
        $categorias = [];
        foreach ($clasificaciones as $keyClasificacion => $valueClasificacion) {
            $categorias[$this->toKey($valueClasificacion->categoria)]['nombre'] = $valueClasificacion->categoria;
            $categorias[$this->toKey($valueClasificacion->categoria)]['id'] = $valueClasificacion->id;
        }

        $indices = IndiceTabla1::all();
        $indice = new IndiceTabla1();
        $monedas = Moneda::getOpciones();

        $publicacion = PublicacionIndice::whereId($publicacion_id)->first();
        $publicaciones = [];
        $i = 0;

        $opciones_indices = PublicacionIndice::getOpcionesIndice($publicacion->moneda_id);
        foreach ($opciones_indices as $keyPublicacion => $valuePublicacion) {
            $publicaciones[$i]['key'] = $keyPublicacion;
            $publicaciones[$i]['mes_anio'] = $valuePublicacion;
            if ($keyPublicacion == $publicacion->id) {
                break;
            }
            $i++;
        }

        return view('publicaciones.indices.create', compact('categorias', 'indices', 'indice', 'publicacion_id', 'monedas', 'publicaciones'));
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  int $publicacion_id
     */
    public function store(Request $request, $publicacion_id)
    {
        $input = $request->all();

        $rules = array(
            'nombre'           => $this->min3max255(),
            'nro'              => 'required',
            'observaciones'    => 'nullable|' . $this->min3max1024(),
        );

        $validator = Validator::make(Input::all(), $rules, $this->validationErrorMessages());

        // Validaciones Custom
        $errores = [];

        // Es nueva
        if (IndiceTabla1::whereNro($input['nro'])->first() != null) {
            $errores['nro'] = trans('mensajes.error.nro_existente');
        }

        if ($input['simple_compuesto'] == 'compuesto') {
            $total = 0;
            foreach ($input['porcentaje'] as $keyPorcentaje => $valuePorcentaje) {

                if ($valuePorcentaje == '%')
                    $errores['porcentaje'][$keyPorcentaje] = trans('validation.required', ['attribute' => trans('forms.porcentaje')]);

                $valuePorcentaje = str_replace("%", "", $valuePorcentaje);
                $valuePorcentaje = str_replace(".", "", $valuePorcentaje);

                if ((int)$valuePorcentaje > 100)
                    $errores['porcentaje'][$keyPorcentaje] = trans('validation_custom.mayor_100', ['attribute' => trans('forms.porcentaje')]);

                $total = $total + $valuePorcentaje;
            }

            if (abs($total - 100) > config('custom.delta')) {
                $errores['simple_compuesto'][0] = trans('mensajes.error.simple_compuesto_suma');
                $errores['div_simple_compuesto'][0] = trans('mensajes.error.simple_compuesto_suma');
                $errores['error_div_simple_compuesto'][0] = trans('mensajes.error.simple_compuesto_suma');
            }
        }


        if ($input['btn_de_publicaciones_anteriores'] == '1') {
            if ($input['sel_pub_anteriores'] == null) {
                $errores['sel_pub_anteriores'][0] = trans('validation_custom.seleccionar_publicacion');
            }
        }

        if ($validator->fails() || sizeof($errores) > 0) {
            $errores = array_merge($errores, $validator->getMessageBag()->toArray());
            $jsonResponse['status'] = false;
            $jsonResponse['errores'] = $errores;
            $jsonResponse['message'] = [];

            return response()->json($jsonResponse);
        }

        $clasificacion = Clasificacion::find($input['sub_categoria_id']);

        $compuesto = false;
        if ($input['simple_compuesto'] == 'compuesto')
            $compuesto = true;

        $no_se_publica = false;
        if (isset($input['no_se_publica']))
            $no_se_publica = true;

        // $aplica_tope = false;
        // if(isset($input['aplica_tope']))
        //   $aplica_tope = true;

        $fuente_id = $input['fuente_id'];
        if ($input['new_fuente'] == 1) {
            $fuente = new Fuente();
            $fuente->nombre = $input['fuente_id'];
            $fuente->save();
            $fuente_id = $fuente->id;
        }

        $publicacion = PublicacionIndice::find($publicacion_id);

        try {
            $indice = IndiceTabla1::create([
                'clasificacion_id'  => $clasificacion->id,
                'nro'               => $input['nro'],
                'nombre'            => $input['nombre'],
                'aplicacion'        => '',
                'fuente_id'         => $fuente_id,
                'moneda_id'         => $publicacion->moneda_id,
                'compuesto'         => $compuesto,
                'no_se_publica'     => $no_se_publica
                // 'aplica_tope'       => $aplica_tope,
            ]);
            $indice->modificado = 1;
            $indice->save();
        } catch (\QueryException $e) {
            Log::error('QueryException', ['Exception' => $e]);
            Session::flash('error', trans('mensajes.error.insert_db'));
            $jsonResponse['status'] = false;
            $jsonResponse['message'] = [trans('mensajes.error.insert_db')];
            return response()->json($jsonResponse);
        }


        if (!isset($input['de_publicaciones_anteriores'])) {
            $valor = $input['valor_inicial'];
            $instancia = InstanciaPublicacionIndice::wherePublicacionId($publicacion_id)->orderBy('batch', 'desc')->first();

            if ($instancia == null)
                $batch = 1;
            else
                $batch = $instancia->batch;

            if ($compuesto) {
                $valor = 0;
                foreach ($input['porcentaje'] as $keyPorcentaje => $valuePorcentaje) {

                    $valor_indice = ValorIndice::whereBatch($batch)
                        ->wherePublicacionId($publicacion_id)
                        ->whereTablaIndicesId($input['indice_compuesto_id'][$keyPorcentaje])
                        ->first()
                        ->valor;

                    $valuePorcentaje = str_replace("%", "", $input['porcentaje'][$keyPorcentaje]);
                    $valuePorcentaje = str_replace(".", "", $valuePorcentaje);

                    if ($valor_indice == '')
                        $valor_indice = 0;

                    $valor = $valor + ($valor_indice * $valuePorcentaje / 100);

                    ConponenteIndiceCompuesto::create([
                        'indice_id'       => $indice->id,
                        'componente_id'   => $input['indice_compuesto_id'][$keyPorcentaje],
                        'porcentaje'      => $valuePorcentaje,
                    ]);
                }
            }
            //  elseif ($calculado) {
            //   $valor = $indice->calcularValor($publicacion_id, $batch);
            // }

            try {
                $valor_indice = new ValorIndice();
                $valor_indice->tabla_indices_id = $indice->id;
                $valor_indice->valor = $valor;
                $valor_indice->variacion = 1;
                $valor_indice->publicacion_id = $publicacion_id;
                $valor_indice->batch = $batch;
                $valor_indice->save();
            } catch (\QueryException $e) {
                Log::error('QueryException', ['Exception' => $e]);
                Session::flash('error', trans('mensajes.error.insert_db'));
                $jsonResponse['status'] = false;
                $jsonResponse['message'] = [trans('mensajes.error.insert_db')];
                return response()->json($jsonResponse);
            }

            $publicaciones = PublicacionIndice::whereId($publicacion_id)->first()->publicaciones_siguientes;
            // por cada publicacion, clonamos el valorIndice creado
            foreach ($publicaciones as $pub) {
                $instancia = $pub->instancias()->orderBy('batch', 'desc')->first();
                if ($instancia == null)
                    $batch = 1;
                else
                    $batch = $instancia->batch;

                $valor_indice = new ValorIndice();
                $valor_indice->tabla_indices_id = $indice->id;
                $valor_indice->valor = 0;
                $valor_indice->variacion = 1;
                $valor_indice->publicacion_id = $pub->id;
                $valor_indice->batch = $batch;
                $valor_indice->save();
            }
        } else {
            foreach ($input['pub_old'] as $keyPubOld => $valuePubOld) {
                $valuePubOld = str_replace(".", "", $valuePubOld);
                $valuePubOld = str_replace(",", ".", $valuePubOld);

                if (!isset($value_ant))
                    $value_ant = $valuePubOld;

                $publicacion = PublicacionIndice::find($keyPubOld);
                $instancia = InstanciaPublicacionIndice::wherePublicacionId($keyPubOld)
                    ->orderBy('batch', 'desc')
                    ->first();

                if ($instancia == null) {
                    $batch = 1;
                } else {
                    $batch = $instancia->batch;
                }

                // creamos los valores indices al pasado de esta manera
                // para respetar los valores que ingreso el usuario

                if ($publicacion->publicado) {
                    $valor_indice = new ValorIndicePublicado();
                } else {
                    $valor_indice = new ValorIndice();
                    $valor_indice->batch = $batch;
                }

                $valor_indice->tabla_indices_id = $indice->id;
                $valor_indice->valor = $valuePubOld;
                $valor_indice->variacion = $valuePubOld / $value_ant;
                $valor_indice->publicacion_id = $publicacion->id;
                $valor_indice->save();

                $value_ant = $valuePubOld;
            }

            // si tenemos publicaciones siguientes a la ultima del array de publicaciones
            // creamos el indice pero con valor vacio, para ser consistentes
            $last_publications = PublicacionIndice::find(array_key_last($input['pub_old']))->publicaciones_siguientes;
            foreach ($last_publications as $pub) {
                $instancia = InstanciaPublicacionIndice::wherePublicacionId($pub->id)->orderBy('batch', 'desc')->first();

                if ($instancia == null)
                    $batch = 1;
                else
                    $batch = $instancia->batch;

                $valor_indice = new ValorIndice();
                $valor_indice->tabla_indices_id = $indice->id;
                $valor_indice->valor = "";
                $valor_indice->variacion = 1;
                $valor_indice->publicacion_id = $pub->id;
                $valor_indice->batch = $batch;
                $valor_indice->save();
            }

            // antiguo metodo de crear indices al pasado

            // IndiceAPublicar::create([
            //     'tabla_indices_id'    => $indice->id,
            //     'publicacion_id'      => $publicacion_id,
            // ]);
        }

        $jsonResponse['status'] = true;
        $jsonResponse['refresh'] = true;
        Session::flash('success', trans('mensajes.dato.indice') . trans('mensajes.success.creado'));
        $jsonResponse['message'] = [trans('mensajes.dato.indice') . trans('mensajes.success.creado')];

        return response()->json($jsonResponse);
    }

    /**
     * @param  int $id
     * @param  int $publicacion_id
     */
    public function edit($id, $publicacion_id)
    {
        $indice = IndiceTabla1::find($id);
        $indice->load('clasificacion');
        $clasificacion = $indice->clasificacion;

        $clasificaciones = Clasificacion::distinct('categoria')->get();
        $categorias = [];

        foreach ($clasificaciones as $categoria) {
            $categorias[$categoria->categoria] = $categoria->categoria;
        }

        $subclasificaciones = Clasificacion::whereCategoria($clasificacion->categoria)->get();
        $subcategorias = [];

        foreach ($subclasificaciones as $subcategoria) {
            $subcategorias[$subcategoria->id] = $subcategoria->subcategoria;
        }

        $indices = IndiceTabla1::all();
        $a_publicar = IndiceAPublicar::where([
            'tabla_indices_id' => $id,
            'publicacion_id' => $publicacion_id,
        ])->first() != null;

        $publicaciones = [];

        if ($a_publicar) {
            $ind_a_publicar = IndiceAPublicar::whereTablaIndicesId($id)->get();
            $i = count($ind_a_publicar);

            foreach ($ind_a_publicar as $keyAPublicar => $valueAPublicar) {
                $publicaciones[$i]['key'] = $valueAPublicar->publicacion_id;
                $publicaciones[$i]['mes_anio'] = $valueAPublicar->publicacion->mes_anio;
                $publicaciones[$i]['value'] = ValorIndice::wherePublicacionId($valueAPublicar->publicacion_id)
                    ->whereTablaIndicesId($id)
                    ->first()
                    ->valor;

                $i--;
            }

            ksort($publicaciones);
        }

        return view(
            'publicaciones.indices.edit',
            compact(
                'categorias',
                'subcategorias',
                'indices',
                'indice',
                'id',
                'publicacion_id',
                'a_publicar',
                'publicaciones'
            )
        );
    }

    /**
     * @param  int $id
     * @param  int $publicacion_id
     */
    public function show($id, $publicacion_id)
    {
        $indice = IndiceTabla1::find($id);

        return view('publicaciones.indices.show', compact('indice'));
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id
     * @param  int $publicacion_id
     */
    public function update(Request $request, $id, $publicacion_id)
    {
        $input = $request->all();

        $rules = [
            'nombre'           => $this->min3max255(),
            'nro'              => 'required',
            'observaciones'    => 'nullable|' . $this->min3max1024(),
            'fuente_id'        => 'required',
        ];
        $validator = Validator::make(Input::all(), $rules, $this->validationErrorMessages());

        // Validaciones Custom
        $errores = [];
        // Es nueva
        if (IndiceTabla1::whereNro($input['nro'])->where('id', '!=', $id)->first() != null) {
            $errores['nro'] = trans('mensajes.error.nro_existente');
        }

        if ($validator->fails() || sizeof($errores) > 0) {
            $errores = array_merge($errores, $validator->getMessageBag()->toArray());
            $jsonResponse['status'] = false;
            $jsonResponse['errores'] = $errores;
            $jsonResponse['message'] = [];
            return response()->json($jsonResponse);
        }

        $valor_indice = IndiceTabla1::find($id);

        $clasificacion = Clasificacion::find($input['sub_categoria_id']);

        $valor_indice->clasificacion_id = $clasificacion->id;
        $valor_indice->nro = $input['nro'];
        $valor_indice->nombre = $input['nombre'];
        $valor_indice->observaciones = $input['observaciones'];
        $valor_indice->modificado = 1;

        $no_se_publica = false;
        if (isset($input['no_se_publica']))
            $no_se_publica = true;

        // $valor_indice->no_se_publica = $no_se_publica;

        try {
            $valor_indice->save();
        } catch (\QueryException $e) {
            Log::error('QueryException', ['Exception' => $e]);
            Session::flash('error', trans('mensajes.error.insert_db'));
            $jsonResponse['status'] = false;
            $jsonResponse['message'] = [trans('mensajes.error.insert_db')];
            return response()->json($jsonResponse);
        }
        if( isset($input['fuente_id'])){
            $fuente = Fuente::find($valor_indice->fuente_id);
            $fuente->nombre = $input['fuente_id'];
            try {
                $fuente->save();
            } catch (\QueryException $e) {
                Log::error('QueryException', ['Exception' => $e]);
                Session::flash('error', trans('mensajes.error.insert_db'));
                $jsonResponse['status'] = false;
                $jsonResponse['message'] = [trans('mensajes.error.insert_db')];
                return response()->json($jsonResponse);
            }
        }

        if (isset($input['pub_old'])) {
            foreach ($input['pub_old'] as $keyPubOld => $valuePubOld) {
                $valuePubOld = str_replace(".", "", $valuePubOld);
                $valuePubOld = str_replace(",", ".", $valuePubOld);
                if (!isset($value_ant))
                    $value_ant = $valuePubOld;

                $publicacion = PublicacionIndice::find($keyPubOld);
                $instancia = InstanciaPublicacionIndice::wherePublicacionId($keyPubOld)->orderBy('batch', 'desc')->first();

                if ($instancia == null)
                    $batch = 1;
                else
                    $batch = $instancia->batch;

                $valor_indice = ValorIndice::whereTablaIndicesId($id)
                    ->wherePublicacionId($publicacion->id)
                    ->first();
                $valor_indice->valor = $valuePubOld;
                $valor_indice->variacion = ($valuePubOld / $value_ant) - 1;
                $valor_indice->batch = $batch;
                $valor_indice->save();

                $value_ant = $valuePubOld;
            }
        }

        // Ver si va
        // if($valor_indice->periodo_actual != $publicacion_id) {
        //   $periodo_actual = $indice->periodo_actual;
        //   $periodo_actual->publicacion_fin_id = $publicacion_id;
        //   try {
        //     $periodo_actual->save();
        //
        //     PeriodoIndiceTabla1::create([
        //       'tabla_indices_id'        => $indice->id,
        //       'publicacion_inicio_id'   => $publicacion_id,
        //       'user_creator_id'         => Auth::user()->id,
        //     ]);
        //   } catch(\QueryException $e) {
        //     Log::error('QueryException', ['Exception' => $e]);
        //     Session::flash('error', trans('mensajes.error.insert_db'));
        //     $jsonResponse['status'] = false;
        //     $jsonResponse['message'] = [trans('mensajes.error.insert_db')];
        //     return response()->json($jsonResponse);
        //   }
        // }


        $jsonResponse['status'] = true;
        $jsonResponse['refresh'] = true;
        Session::flash('success', trans('mensajes.dato.indice') . trans('mensajes.success.actualizado'));
        $jsonResponse['message'] = [trans('mensajes.dato.indice') . trans('mensajes.success.actualizado')];
        return response()->json($jsonResponse);
    }

    /**
     * @param  int $id
     */
    public function deshabilitar($id, $publicacion_id)
    {
        $indice = IndiceTabla1::find($id);
        $periodo_actual = $indice->periodo_actual;
        $periodo_actual->publicacion_fin_id = $publicacion_id;

        try {
            $periodo_actual->save();
        } catch (QueryException $e) {
            Log::error('QueryException', ['Exception' => $e]);
            Session::flash('error', trans('mensajes.error.insert_db'));
            $jsonResponse['status'] = false;
            $jsonResponse['message'] = [trans('mensajes.error.insert_db')];
            return response()->json($jsonResponse);
        }

        $jsonResponse['status'] = true;
        $jsonResponse['refresh'] = true;
        Session::flash('success', trans('mensajes.dato.indice') . trans('mensajes.success.deshabilitado'));
        $jsonResponse['message'] = [trans('mensajes.dato.indice') . trans('mensajes.success.deshabilitado')];
        return response()->json($jsonResponse);
    }

    /**
     * @param  int $id
     */
    public function validarDeshabilitar($id, $publicacion_id)
    {
        $indice = IndiceTabla1::find($id);

        // Valido que no se use en un contrato activo
        foreach ($indice->composiciones as $keyComp => $valueComp) {
            $contrato = $valueComp->polinomica->contrato_moneda->contrato;

            if (!$contrato->recepcionado) {
                $jsonResponse['status'] = false;
                $jsonResponse['alert']['title'] =  trans('forms.error_al') . ' ' . strtolower(trans('index.deshabilitar') . ' ' . trans('index.indice'));
                $jsonResponse['alert']['message'] = trans('publicaciones.errores.deshabilitar_indice.contrato_activo', ['contrato' => $contrato->nombre_completo]);
                return response()->json($jsonResponse);
            }
        }
        // FIN Valido que no se use en un contrato activo

        // Valido que no sea parte de un componente del Analisis de Item
        $categorias = $indice->componentes_analisis->pluck('categoria_id', 'categoria_id');

        $categorias = $indice->componentes_analisis()->select('categoria_id')->groupBy('categoria_id')->with('categoria')->get();

        $contratos = [];
        foreach ($categorias as $keyCat => $valueCat) {
            if (!isset($contratos[$valueCat->categoria->analisis_item_id]))
                $contratos[$valueCat->categoria->analisis_item_id] = $valueCat->categoria->analisis_item->contrato;
        }

        foreach ($contratos as $keyContrato => $valueContrato) {
            if (!$valueContrato->recepcionado) {
                $jsonResponse['status'] = false;
                $jsonResponse['alert']['title'] =  trans('forms.error_al') . ' ' . strtolower(trans('index.deshabilitar') . ' ' . trans('index.indice'));
                $jsonResponse['alert']['message'] = trans('publicaciones.errores.deshabilitar_indice.analisis_precios', ['contrato' => $valueContrato->nombre_completo]);
                return response()->json($jsonResponse);
            }
        }
        // FIN Valido que no sea parte de un componente del Analisis de Item

        // Valido que no sea componente de uno compuesto
        if (sizeof($indice->padre) > 0) {
            $jsonResponse['status'] = false;
            $jsonResponse['alert']['title'] =  trans('forms.error_al') . ' ' . strtolower(trans('index.deshabilitar') . ' ' . trans('index.indice'));

            $jsonResponse['alert']['message'] = trans('publicaciones.errores.deshabilitar_indice.es_componente', ['padre' => $indice->padre->first()->indice_tabla1->nro]);
            return response()->json($jsonResponse);
        }

        $jsonResponse['status'] = true;
        $jsonResponse['ok']['title'] =  trans('index.deshabilitar') . ' ' . trans('index.indice');
        $jsonResponse['ok']['message'] = trans('publicaciones.mensajes.deshabilitar_indice', ['nombre' => $indice->nombre]);
        $jsonResponse['ok']['action'] = route('indices.deshabilitar', ['id' => $id, 'publicacion_id' => $publicacion_id]);

        return response()->json($jsonResponse);
    }

    // Funcion para calcular el select de subcategoria onchange del de categoria
    /**
     * @param string $id
     */
    public function getSubCategorias($id)
    {
        $clasificaciones = Clasificacion::whereCategoria($id)->get();

        $jsonResponse['select_placeholder']['sub_categoria_id'] = trans('forms.select.sub_categoria');

        foreach ($clasificaciones as $keySubCategoria => $valueClasificacion) {
            $jsonResponse['select']['sub_categoria_id'][$valueClasificacion->id]['id'] = $valueClasificacion->id;
            $jsonResponse['select']['sub_categoria_id'][$valueClasificacion->id]['nombre'] = $valueClasificacion->subcategoria;
        }

        return response()->json($jsonResponse);
    }
}
