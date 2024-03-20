<?php

namespace App\Http\Controllers;

use App\Jobs\CalculoVariacionEnPublicacion;
use Carbon\Carbon;
use DateTime;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Indice\Clasificacion;
use Indice\IndiceAPublicar;
use Indice\IndiceTabla1;
use Indice\InstanciaPublicacionIndice;
use Indice\PeriodoIndiceTabla1;
use Indice\PublicacionIndice;
use Indice\ValorIndice;
use Indice\ValorIndicePublicado;
use Maatwebsite\Excel\Facades\Excel;
use Response;
use View;
use Yacyreta\Mes;
use Yacyreta\Moneda;

class PublicacionesController extends Controller
{

    private $rules;

    public function __construct()
    {
        View::share('ayuda', 'indices');
        $this->middleware('auth', ['except' => 'logout']);
        $this->rules = [];
        $this->rules['guardar_borrador'] = [
            'valor.*' => 'numeric|min:0|nullable',
        ];
        $this->rules['enviar_aprobar'] = [
            'valor.*' => 'required|numeric|min:0',
        ];
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     */
    public function index(Request $request)
    {
        $input = $request->all();
        $search_input = '';
        $publicaciones = PublicacionIndice::orderBy('anio', 'desc')->orderBy('mes', 'desc')->get();

        if ($request->getMethod() != "GET") {
            $search_input = $input['search_input'];
            $input_lower = $this->minusculaSinAcentos($input['search_input']);

            if ($input_lower != '') {
                $publicaciones = $publicaciones->filter(function ($publicacion) use ($input_lower) {
                    if ($publicacion->publicado) {
                        $publicado = trans('index.publicado');
                    } else {
                        $publicado = trans('index.no_publicado');
                    }

                    return substr_count(
                        $this->minusculaSinAcentos($publicado),
                        $input_lower
                    ) > 0 || substr_count(
                        $this->minusculaSinAcentos($publicacion->mes_anio),
                        $input_lower
                    ) > 0 || substr_count(
                        $this->minusculaSinAcentos($publicacion->estado_nombre_color['nombre']),
                        $input_lower
                    ) > 0 || substr_count(
                        $this->minusculaSinAcentos($publicacion->publicador_nombre_apellido),
                        $input_lower
                    ) > 0 || substr_count(
                        $this->minusculaSinAcentos($publicacion->fecha_publicacion),
                        $input_lower
                    ) > 0;
                });
            }
        }

        $publicaciones = $this->paginateCustom($publicaciones);

        $ultima_publicacion = PublicacionIndice::last();
        if ($ultima_publicacion != null) {
            $mes_anio = $ultima_publicacion->mes_anio_siguientes;

            $prefix = '';
            if ($mes_anio['mes_siguiente'] < 10) {
                $prefix = '0';
            }
            $mes_anio = $prefix . $mes_anio['mes_siguiente'] . '/' . $mes_anio['anio_siguiente'];
        } else {
            $mes_anio = date('m') . '/' . date('Y');
        }

        $monedas = $publicaciones->filter(function ($value, $key) {
            return $value->moneda != null;
        })->map(function ($value, $key) {
            return $value->moneda;
        })->unique();

        return view('publicaciones.index', compact('publicaciones', 'mes_anio', 'search_input', 'monedas'));
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     */
    public function filtrarPorMoneda(Request $request, $id)
    {
        $input = $request->all();
        $search_input = '';
        $publicaciones = PublicacionIndice::whereMonedaId($id)->orderBy('anio', 'desc')->orderBy('mes', 'desc')->get();

        if ($request->getMethod() != "GET") {
            $search_input = $input['search_input'];
            $input_lower = $this->minusculaSinAcentos($input['search_input']);

            if ($input_lower != '') {
                $publicaciones = $publicaciones->filter(function ($publicacion) use ($input_lower) {
                    if ($publicacion->publicado) {
                        $publicado = trans('index.publicado');
                    } else {
                        $publicado = trans('index.no_publicado');
                    }

                    return substr_count($this->minusculaSinAcentos($publicado), $input_lower) > 0
                        || substr_count($this->minusculaSinAcentos($publicacion->mes_anio), $input_lower) > 0
                        || substr_count($this->minusculaSinAcentos($publicacion->estado_nombre_color['nombre']), $input_lower) > 0
                        || substr_count($this->minusculaSinAcentos($publicacion->publicador_nombre_apellido), $input_lower) > 0
                        || substr_count($this->minusculaSinAcentos($publicacion->fecha_publicacion), $input_lower) > 0;
                });
            }
        }

        $publicaciones = $this->paginateCustom($publicaciones);

        $ultima_publicacion = PublicacionIndice::last();
        if ($ultima_publicacion != null) {
            $mes_anio = $ultima_publicacion->mes_anio_siguientes;

            $prefix = '';

            if ($mes_anio['mes_siguiente'] < 10) {
                $prefix = '0';
            }

            $mes_anio = $prefix . $mes_anio['mes_siguiente'] . '/' . $mes_anio['anio_siguiente'];
        } else {
            $mes_anio = date('m') . '/' . date('Y');
        }

        $monedas = Moneda::all();

        return view('publicaciones.index', compact('publicaciones', 'mes_anio', 'search_input', 'monedas'));
    }

    /**
     * @param  int  $id
     */
    public function publicacionesDisponiblesPorMoneda($id)
    {
        $inicio = PublicacionIndice::orderBy('anio', 'asc')->orderBy('mes', 'asc')->get()->first();

        $inicio = strtotime(date(strval($inicio->anio) . '-' . strval($inicio->mes . '-01')));

        $fin = strtotime(date("Y-m-d"));

        while ($inicio < $fin) {
            $mes = date('m', $inicio);
            $anio = date('Y', $inicio);

            if ($mes < 10) {
                $select_options[substr($mes, -1) . '-' . $anio] = $mes . '/' . $anio;
            } else {
                $select_options[$mes . '-' . $anio] = $mes . '/' . $anio;
            }

            $inicio = strtotime("+1 month", $inicio);
        }

        $publicaciones = PublicacionIndice::whereMonedaId($id)
            ->orderBy('anio', 'desc')
            ->orderBy('mes', 'desc')
            ->get()
            ->map
            ->only('anio', 'mes')
            ->unique()
            ->values()
            ->toArray();

        $fechas = array_map(function ($a) {
            $date = strtotime(date($a['anio'] . '/' . $a['mes'] . '/01'));
            $mes = date('m', $date);
            $anio = date('Y', $date);

            if ($mes < 10) {
                return [substr(date('m', $date), -1) . '-' . $anio => $mes . '/' . $anio];
            } else {
                return [$mes . '-' . $anio => $mes . '/' . $anio];
            }
        }, $publicaciones);

        if (!empty($fechas)) {
            $fechas = array_merge(...$fechas);
        }

        $options = array_diff($select_options, $fechas);

        return response()->json(compact('options'));
    }

    public function create()
    {
        $ultima_publicacion = PublicacionIndice::last();

        if ($ultima_publicacion != null) {
            $mes_anio = PublicacionIndice::last()->mes_anio_siguientes;

            if ($mes_anio['mes_siguiente'] < 10) {
                $mes = '0' . $mes_anio['mes_siguiente'];
            } else {
                $mes = $mes_anio['mes_siguiente'];
            }

            $inicio = strtotime($mes_anio['anio_siguiente'] . '-' . $mes . '-01');
        } else {
            $inicio = strtotime(date("Y-m", strtotime("-6 months")) . '-01');
        }

        $fin = strtotime(date('Y-m-d'));

        if (config('custom.test_mode') == 'true') {
            $fin = strtotime(date('Y-m-d', strtotime("+6 months")));
        }

        while ($inicio < $fin) {
            $mes = date('m', $inicio);
            $anio = date('Y', $inicio);
            if ($mes < 10) {
                $select_options[substr($mes, -1) . '-' . $anio] = $mes . '/' . $anio;
            } else {
                $select_options[$mes . '-' . $anio] = $mes . '/' . $anio;
            }
            $inicio = strtotime("+1 month", $inicio);
        }

        $moneda_options = Moneda::all()->pluck('simbolo', 'id')->toArray();

        return view('publicaciones.create', compact('select_options', 'moneda_options'));
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $mes_anio = explode('-', $input['mes_anio']);
        $moneda_id = (int) $input['moneda'];

        if (PublicacionIndice::whereMes($mes_anio[0])->whereAnio($mes_anio[1])->whereMonedaId($moneda_id)->first()) {
            $jsonResponse['status'] = false;
            $jsonResponse['errores']['mes_anio'] = trans('publicaciones.publicacion_existente');
            $jsonResponse['message'] = [];
            return response()->json($jsonResponse);
        }

        try {
            $publicacion = PublicacionIndice::create([
                'mes' => $mes_anio[0],
                'anio' => $mes_anio[1],
                'user_creator_id' => Auth::user()->id,
                'moneda_id' => $moneda_id
            ]);
        } catch (QueryException $e) {
            Log::error('QueryException', ['Exception' => $e]);
            Session::flash('error', trans('mensajes.error.insert_db'));
            $jsonResponse['status'] = false;
            $jsonResponse['message'] = [trans('mensajes.error.insert_db')];
            return response()->json($jsonResponse);
        }

        try {
            $instancia_nueva = InstanciaPublicacionIndice::create([
                'estado' => 'nueva', 'observaciones' => '', 'publicacion_id' => $publicacion->id, 'batch' => 0,
                'user_creator_id' => Auth::user()->id
            ]);

            $instancia_borrador = InstanciaPublicacionIndice::create([
                'estado' => 'guardar_borrador',
                'observaciones' => '',
                'publicacion_id' => $publicacion->id,
                'batch' => 1,
                'user_creator_id' => Auth::user()->id
            ]);
        } catch (QueryException $e) {
            Log::error('QueryException', ['Exception' => $e]);
            Session::flash('error', trans('mensajes.error.insert_db'));
            $jsonResponse['status'] = false;
            $jsonResponse['message'] = [trans('mensajes.error.insert_db')];
            return response()->json($jsonResponse);
        }

        $valores_indices = [];
        $now = date("Y-m-d H:i:s");
        $publicacion_id = $publicacion->id;

        foreach (IndiceTabla1::all() as $keyValorIndice => $valueValorIndice) {
            $valores_indices[] = ([
                'tabla_indices_id' => $valueValorIndice->id, 'valor' => '', 'variacion' => '',
                'user_creator_id' => Auth::user()->id, 'publicacion_id' => $publicacion_id, 'batch' => 1,
                'updated_at' => $now, 'created_at' => $now,
            ]);
        }

        try {
            ValorIndice::insert($valores_indices);
        } catch (\QueryException $e) {
            Log::error('QueryException', ['Exception' => $e]);
            Session::flash('error', trans('mensajes.error.insert_db'));
            $jsonResponse['status'] = false;
            $jsonResponse['message'] = [trans('mensajes.error.insert_db')];
            return response()->json($jsonResponse);
        }

        Session::flash('success', trans('mensajes.dato.publicacion') . trans('mensajes.success.creada'));
        $jsonResponse['message'] = [trans('mensajes.dato.publicacion') . trans('mensajes.success.creada')];
        $jsonResponse['status'] = true;
        $jsonResponse['url'] = route('publicaciones.edit', ['id' => $publicacion->id]);
        return response()->json($jsonResponse);
    }

    /**
     * @param  int  $id
     */
    public function edit($id)
    {
        $publicacion = PublicacionIndice::findOrFail($id);

        if ($publicacion->publicado || $publicacion->hay_publicaciones_publicadas_siguientes) {
            return redirect()->route('publicaciones.show', ['id' => $id]);
        }

        $valores = $publicacion->valores_por_categoria;
        $moneda = Moneda::findOrFail($publicacion->moneda_id);

        if ($moneda) {
            $valores_filtrados = array_filter(
                $valores,
                function ($var) use ($moneda) {
                    return $var["moneda_id"] == $moneda->id;
                }
            );
        } else {
            $valores_filtrados = $valores;
        }

        $es_borrador = $publicacion->inputs_editables;

        $categorias = Clasificacion::select('id', 'subcategoria', 'categoria')
            ->get()
            ->keyBy('id')
            ->map(function (
                $categoria,
                $key
            ) {
                return [
                    'categoria' => $categoria->categoria,
                    'subcategoria' => $categoria->subcategoria
                ];
            });

        $valores_por_categoria = [];

        foreach ($valores_filtrados as $keyMoneda => $valueMoneda) {
            $valores_por_categoria[$keyMoneda]['moneda_id'] = $valueMoneda['moneda_id'];
            $valores_por_categoria[$keyMoneda]['moneda_key'] = $valueMoneda['moneda_key'];
            $valores_por_categoria[$keyMoneda]['moneda'] = $valueMoneda['moneda'];
            $valores_por_categoria[$keyMoneda]['valores'] = [];

            foreach ($valueMoneda['valores'] as $keyValorPorCat => $valueValorPorCat) {
                $categoria = $categorias[$keyValorPorCat]['categoria'];
                $subcategoria = $categorias[$keyValorPorCat]['subcategoria'];

                foreach ($valueValorPorCat as $keyValor => $valueValor) {
                    $valores_por_categoria[$keyMoneda]['valores'][$categoria][$subcategoria][] = $valueValor;
                }
            }
        }

        return view('publicaciones.edit', compact(
            'publicacion',
            'valores_por_categoria',
            'es_borrador'
        ));
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     */
    public function update(Request $request, $id)
    {
        $publicacion = PublicacionIndice::find($id);
        $user = Auth::user();

        if ($publicacion->publicado) {
            $jsonResponse['message'] = trans('publicaciones.errores.publicado');
            Log::error(
                trans('index.error403'),
                ['User' => $user, 'id' => $id, 'Error' => trans('publicaciones.errores.publicado')]
            );
            $jsonResponse['status'] = false;
            return response()->json($jsonResponse);
        }

        // Falta validacion de "es numero"

        $input = $request->all();
        $accion = $input['accion'];

        if (!($user->can('publicacion-' . $accion) && in_array($accion, $publicacion->acciones))) {
            $jsonResponse['message'] = trans('index.error403');
            Log::error(trans('index.error403'), ['User' => $user, 'Accion' => $accion]);
            $jsonResponse['status'] = false;
            return response()->json($jsonResponse);
        }

        $custom_validation = $this->customValidate($accion, $input);
        if (sizeof($custom_validation) > 0) {
            $jsonResponse['status'] = false;
            $jsonResponse['errores'] = $custom_validation;
            $jsonResponse['message'] = [];
            return response()->json($jsonResponse);
        }

        $response = $this->$accion($id, $input);
        if ($response['status'] == true) {

            $observaciones = '';
            if (isset($response['observaciones'])) {
                $observaciones = $response['observaciones'];
            }

            try {
                $instancia_publicacion = InstanciaPublicacionIndice::create([
                    'estado' => $accion, 'observaciones' => $observaciones, 'publicacion_id' => $publicacion->id,
                    'batch' => $response['batch'], 'user_creator_id' => Auth::user()->id
                ]);
            } catch (QueryException $e) {
                Log::error('QueryException', ['Exception' => $e]);
                Session::flash('error', trans('mensajes.error.insert_db'));
                $jsonResponse['status'] = false;
                $jsonResponse['message'] = [trans('mensajes.error.insert_db')];
                return response()->json($jsonResponse);
            }
        }

        return response()->json($response);
    }

    /**
     * @param  string  $accion
     * @param  array  $input
     */
    private function customValidate($accion, $input)
    {
        $errores = [];
        if ($accion == 'enviar_aprobar') {
            foreach ($input['valor'] as $keyValor => $valueValor) {
                $indice = IndiceTabla1::find($keyValor);
                if (($valueValor == '' || $valueValor == null) && $indice->se_usa) {
                    $nro = $indice->nro;
                    $errores[$keyValor] = trans(
                        'publicaciones.errores.se_usa_en_polinomica',
                        ['nro' => $indice->nro, 'nombre' => $indice->nombre]
                    );
                }
            }
        }
        return $errores;
    }

    /**
     * @param  string  $accion
     * @param  int  $id
     * @param  \Illuminate\Http\Request  $request
     */
    public function preValidacion($accion, $id, Request $request)
    {
        $input = $request->all();
        $errores = [];
        $publicacion = PublicacionIndice::find($id);

        if (!$publicacion->puede_editarse) {
            $jsonResponse['status'] = false;
            $publicacion_posterior = $publicacion->publicaciones_publicadas_siguientes->first()->mes_anio;
            $jsonResponse['alert']['title'] = trans('publicaciones.instancia.acciones.publicar') . ' ' . $publicacion->mes_anio;
            $accion = trans('publicaciones.instancia.acciones.' . $accion);
            $jsonResponse['alert']['message'] = trans(
                'publicaciones.errores.publicados_siguientes_accion',
                [
                    'publicacion_posterior' => $publicacion_posterior,
                    'accion' => $accion
                ]
            );
            return response()->json($jsonResponse);
        }

        if ($accion == 'enviar_aprobar') {
            $hay_errores = false;

            foreach ($input['valor'] as $keyValor => $valueValor) {
                $indice = IndiceTabla1::find($keyValor);

                if ($valueValor == '' || $valueValor == null) {
                    $hay_errores = true;

                    $errores[$keyValor] = trans(
                        'publicaciones.errores.se_usa_en_polinomica',
                        [
                            'nro' => $indice->nro,
                            'nombre' => $indice->nombre,
                        ]
                    );
                }
            }

            if (!$hay_errores) {
                $jsonResponse['status'] = true;
                $jsonResponse['ok']['title'] = trans('publicaciones.instancia.acciones.' . $accion) . ' ' . $publicacion->mes_anio;
                $jsonResponse['ok']['message'] = trans('publicaciones.mensajes.enviar_aprobar');
                $jsonResponse['ok']['route'] = route('publicaciones.update', ['id' => $id]);

                return response()->json($jsonResponse);
            }
        } else if ($accion == 'publicar') {
            if ($publicacion->hay_publicaciones_publicadas_siguientes) {
                $jsonResponse['status'] = false;
                $publicacion_posterior = $publicacion->publicaciones_publicadas_siguientes->first()->mes_anio;
                $jsonResponse['alert']['title'] = trans('publicaciones.instancia.acciones.publicar') . ' ' . $publicacion->mes_anio;
                $jsonResponse['alert']['message'] = trans(
                    'publicaciones.errores.publicados_siguientes',
                    ['publicacion_posterior' => $publicacion_posterior]
                );

                return response()->json($jsonResponse);
            }

            if ($publicacion->hay_publicaciones_no_publicadas_anteriores) {
                $jsonResponse['status'] = false;
                $publicacion_anterior = $publicacion->publicaciones_no_publicadas_anteriores->first()->mes_anio;
                $jsonResponse['alert']['title'] = trans('publicaciones.instancia.acciones.publicar') . ' ' . $publicacion->mes_anio;
                $jsonResponse['alert']['message'] = trans(
                    'publicaciones.errores.publicados_anteriores',
                    ['mes' => $publicacion_anterior]
                );

                return response()->json($jsonResponse);
            }

            $jsonResponse['ok']['title'] = trans('publicaciones.instancia.acciones.publicar') . ' ' . $publicacion->mes_anio;
            $jsonResponse['ok']['message'] = trans('publicaciones.mensajes.confirmar_publicar');
            $jsonResponse['ok']['route'] = route('publicaciones.publicar', ['id' => $id]);
        }

        if (sizeof($errores) > 0) {
            $jsonResponse['status'] = false;
            $jsonResponse['errores'] = $errores;
        } else {
            $jsonResponse['status'] = true;
        }

        return response()->json($jsonResponse);
    }

    /**
     * @param  int  $id
     * @param  array  $input
     */
    private function guardar_borrador($id, $input)
    {
        $instancia = InstanciaPublicacionIndice::wherePublicacionId($id)->orderBy('batch', 'desc')->first();

        $valores_indices = [];
        $now = date("Y-m-d H:i:s");

        $publicacion = PublicacionIndice::find($id);
        $publicacion_anterior = $publicacion->ultima_publicacion_publicada;
        $valores_anteriores = [];

        if ($publicacion_anterior != null) {
            $hay_anterior = true;
            foreach ($publicacion_anterior->valores_publicados as $keyValorPub => $valueValorPub) {
                $valores_anteriores[$valueValorPub->tabla_indices_id] = $valueValorPub->valor;
            }
        } else {
            $hay_anterior = false;
        }

        $calculados = [];

        if ($instancia == null) {
            $batch = 1;
        } else {
            $batch = $instancia->batch + 1;
        }

        foreach ($input['valor'] as $keyValor => $valueValor) {
            $indice = IndiceTabla1::find($keyValor);

            if (!$indice->compuesto && !$indice->calculado) {
                $valueValor = str_replace(".", "", $valueValor);
                $valueValor = str_replace(",", ".", $valueValor);
                $valor = $valueValor;

                $valueValor = str_replace(".", "", $valueValor);
                $valueValor = substr_replace($valueValor, '.', strlen($valueValor) - 2, 0);
            } else {
                if ($indice->compuesto) {
                    $componentes = $indice->componentes;
                    $valor = 0;

                    foreach ($componentes as $keyComp => $valueComp) {
                        $val_sin_separador = str_replace(".", "", $input['valor'][$valueComp->componente_id]);
                        $val_sin_separador = str_replace(",", "", $val_sin_separador);
                        $val_sin_separador = substr_replace($val_sin_separador, '.', strlen($val_sin_separador) - 2, 0);

                        $valor = $valor + $val_sin_separador * ($valueComp->porcentaje / 100);
                    }

                    $valor = number_format($valor, 2, '.', ',');
                    $valor = str_replace(",", "", $valor);
                } elseif ($indice->calculado) {
                    $calculados[$keyValor] = $valueValor;
                }
            }

            if (!$indice->calculado) {
                $variacion = 0;

                if ($valor == null) {
                    $valor = '';
                }

                if ($hay_anterior) {
                    $variacion = 0;

                    if (isset($valores_anteriores[$keyValor]) && $valor != '') {
                        $valor_anterior = $valores_anteriores[$keyValor];

                        if ($valor_anterior != 0) {
                            $valor = str_replace(".", "", $valor);
                            $valor = str_replace(",", "", $valor);

                            $valor = substr_replace($valor, '.', strlen($valor) - 2, 0);
                            $variacion = ($valor / $valor_anterior) - 1;
                        }
                    }
                }
            }

            $valor_simple = [];

            if (!$indice->calculado) {
                $valor_simple[$keyValor] = '' . $valor;
                $variacion = $variacion + 1;

                $valores_indices[] = ([
                    'tabla_indices_id' => $keyValor,
                    'valor' => '' . $valor,
                    'variacion' => '' . $variacion,
                    'user_creator_id' => Auth::user()->id,
                    'publicacion_id' => $id,
                    'batch' => $batch,
                    'updated_at' => $now,
                    'created_at' => $now,
                ]);
            }
        }

        try {
            ValorIndice::insert($valores_indices);
        } catch (\QueryException $e) {
            Log::error('QueryException', ['Exception' => $e]);
            $mensaje = trans('mensajes.error.insert_db');

            Session::flash('error', $mensaje);
            $response['status'] = false;
            $response['message'] = [$mensaje];
            return $response;
        }

        $valores_indices = [];

        if (sizeof($calculados) > 0) {
            foreach ($calculados as $keyValor => $valueValor) {
                $indice = IndiceTabla1::find($keyValor);
                $valor = $indice->calcularValor($id, $batch);

                if ($valor != '') {
                    $valor = number_format($valor, 3);
                }

                $valor = str_replace(",", "", $valor);

                $variacion = 0;

                if ($hay_anterior) {
                    if (isset($valores_anteriores[$keyValor]) && $valueValor != '') {
                        $valor_anterior = $valores_anteriores[$keyValor];

                        if ($valor_anterior != 0) {
                            $valor = str_replace(".", "", $valor);
                            $valor = str_replace(",", "", $valor);

                            $valor = substr_replace($valor, '.', strlen($valor) - 3, 0);
                            $variacion = ($valor / $valor_anterior) - 1;

                            Log::error('Carretero V', ['$valor ' => $valor]);
                            Log::error('Carretero VA', ['$valor_anterior' => $valor_anterior]);
                        }
                    }
                }

                $variacion = $variacion + 1;

                $valores_indices[] = ([
                    'tabla_indices_id' => $keyValor,
                    'valor' => '' . $valor,
                    'variacion' => '' . $variacion,
                    'user_creator_id' => Auth::user()->id,
                    'publicacion_id' => $id,
                    'batch' => $batch,
                    'updated_at' => $now,
                    'created_at' => $now,
                ]);
            }
        }

        try {
            ValorIndice::insert($valores_indices);
        } catch (\QueryException $e) {
            Log::error('QueryException', ['Exception' => $e]);
            $mensaje = trans('mensajes.error.insert_db');
            Session::flash('error', $mensaje);
            $response['status'] = false;
            $response['message'] = [$mensaje];
            return $response;
        }

        $response['status'] = true;
        $response['batch'] = $batch;

        $response['message'] = [trans('publicaciones.mensajes.borrador_guardado')];
        return $response;
    }

    /**
     * @param  int  $id
     * @param  array  $input
     */
    private function enviar_aprobar($id, $input)
    {
        $instancia = InstanciaPublicacionIndice::wherePublicacionId($id)->orderBy('batch', 'desc')->first();

        if ($instancia == null) {
            $batch = 1;
        } else {
            $batch = $instancia->batch + 1;
        }

        $valores_indices = [];
        $now = date("Y-m-d H:i:s");

        $publicacion = PublicacionIndice::find($id);
        $publicacion_anterior = $publicacion->ultima_publicacion_publicada;
        $valores_anteriores = [];

        if ($publicacion_anterior != null) {
            $hay_anterior = true;
            foreach ($publicacion_anterior->valores_publicados as $valueValorPub) {
                $valores_anteriores[$valueValorPub->tabla_indices_id] = $valueValorPub->valor;
            }
        } else {
            $hay_anterior = false;
        }

        foreach ($input['valor'] as $keyValor => $valueValor) {
            $indice = IndiceTabla1::find($keyValor);

            if (!$indice->compuesto && !$indice->calculado) {
                $valueValor = str_replace(".", "", $valueValor);
                $valueValor = str_replace(",", ".", $valueValor);
                $valor = $valueValor;

                $valueValor = str_replace(".", "", $valueValor);
                $valueValor = substr_replace($valueValor, '.', strlen($valueValor) - 2, 0);
            } elseif ($indice->compuesto) {
                $componentes = $indice->componentes;
                $valor = 0;

                foreach ($componentes as $valueComp) {
                    $val_sin_separador = str_replace(".", "", $input['valor'][$valueComp->componente_id]);
                    $val_sin_separador = str_replace(",", "", $val_sin_separador);
                    $val_sin_separador = substr_replace($val_sin_separador, '.', strlen($val_sin_separador) - 2, 0);

                    $valor = $valor + $val_sin_separador * ($valueComp->porcentaje / 100);
                }

                $valor = number_format($valor, 2, '.', ',');
                $valor = str_replace(",", "", $valor);
            } elseif ($indice->calculado) {
                $calculados[$keyValor] = $valueValor;
            }

            if (!$indice->calculado) {
                $variacion = 0;

                if ($valor == null) {
                    $valor = '';
                }

                if ($hay_anterior) {
                    if (isset($valores_anteriores[$keyValor])) {
                        if ($valor != '') {
                            $valor_anterior = $valores_anteriores[$keyValor];

                            if ($valor_anterior != 0) {
                                $valor = str_replace(".", "", $valor);
                                $valor = str_replace(",", "", $valor);

                                $valor = substr_replace($valor, '.', strlen($valor) - 2, 0);
                                $variacion = ($valor / $valor_anterior) - 1;
                            }
                        } else {
                            $variacion = '';
                        }
                    }
                }
            }

            $valor_simple = [];

            if (!$indice->calculado) {
                $valor_simple[$keyValor] = '' . $valor;
                $variacion = $variacion + 1;

                $valores_indices[] = [
                    'tabla_indices_id' => $keyValor,
                    'valor' => '' . $valor,
                    'variacion' => '' . $variacion,
                    'user_creator_id' => Auth::user()->id,
                    'publicacion_id' => $id,
                    'batch' => $batch,
                    'updated_at' => $now,
                    'created_at' => $now,
                ];
            }
        }

        try {
            ValorIndice::insert($valores_indices);
        } catch (\QueryException $e) {
            Log::error('QueryException', ['Exception' => $e]);
            $mensaje = trans('mensajes.error.insert_db');
            Session::flash('error', $mensaje);
            $response['status'] = false;
            $response['message'] = [$mensaje];
            return $response;
        }

        $valores_indices = [];
        $calculados = [];

        if (isset($calculados) && sizeof($calculados) > 0) {
            foreach ($calculados as $keyValor => $valueValor) {
                $indice = IndiceTabla1::find($keyValor);
                $valor = $indice->calcularValor($id, $batch);

                if ($valor != '') {
                    $valor = number_format($valor, 3);
                }

                $valor = str_replace(",", "", $valor);

                $variacion = 0;

                if ($hay_anterior && isset($valores_anteriores[$keyValor]) && $valueValor != '') {
                    $valor_anterior = $valores_anteriores[$keyValor];

                    if ($valor_anterior != 0) {
                        $valor = str_replace(".", "", $valor);
                        $valor = str_replace(",", "", $valor);

                        $valor = substr_replace($valor, '.', strlen($valor) - 3, 0);
                        $variacion = ($valor / $valor_anterior) - 1;
                    }
                }

                $variacion = $variacion + 1;

                $valores_indices[] = ([
                    'tabla_indices_id' => $keyValor,
                    'valor' => '' . $valor,
                    'variacion' => '' . $variacion,
                    'user_creator_id' => Auth::user()->id,
                    'publicacion_id' => $id,
                    'batch' => $batch,
                    'updated_at' => $now,
                    'created_at' => $now,
                ]);
            }
        }

        try {
            ValorIndice::insert($valores_indices);
        } catch (\QueryException $e) {
            Log::error('QueryException', ['Exception' => $e]);
            $mensaje = trans('mensajes.error.insert_db');

            Session::flash('error', $mensaje);
            $response['status'] = false;
            $response['message'] = [$mensaje];
            return $response;
        }

        try {
            $publicacion->save();
        } catch (\QueryException $e) {
            Log::error('QueryException', ['Exception' => $e]);
            $mensaje = trans('mensajes.error.insert_db');

            Session::flash('error', $mensaje);
            $response['status'] = false;
            $response['message'] = [$mensaje];
            return $response;
        }

        $publicacion->sendEnviadoAprobarNotification();

        $response['status'] = true;
        $response['batch'] = $batch;

        $response['message'] = [trans('publicaciones.mensajes.enviada_aprobar')];
        return $response;
    }

    /**
     * @param  int  $id
     * @param  array  $input
     */
    public function publicar($id)
    {
        // Obtine la publicación
        $publicacion = PublicacionIndice::find($id);
        // Marca como publicado
        $publicacion->publicado = true;
        // Recupera el usuario de la sesión
        $user_id = Auth::user()->id;

        $indices = $publicacion->indices_a_publicar;

        // Valores de publicaciones anteriores
        // posiblemente sin uso (deprecar/refactorizar?)
        foreach ($indices as $valueIndice) {
            // Obtiene el id del indice a publicar
            $tabla_indices_id = $valueIndice->tabla_indices_id;

            // Recupera los valores del indice para la publicación
            $valores = ValorIndice::whereTablaIndicesId($tabla_indices_id)
                ->where('publicacion_id', '!=', $id)
                ->get();

            // Toma el primer valor del indice
            $valorFirst = $valores->first();

            if ($valorFirst) {

                // Inicio un nuevo periodo
                try {
                    PeriodoIndiceTabla1::create([
                        'tabla_indices_id' => $tabla_indices_id,
                        'publicacion_inicio_id' => $valorFirst->publicacion_id,
                        'nro' => $valueIndice->indice_tabla1->nro,
                        'clasificacion_id' => $valueIndice->indice_tabla1->clasificacion_id,
                        'nombre' => $valueIndice->indice_tabla1->nombre,
                        'aplicacion' => $valueIndice->indice_tabla1->aplicacion,
                        'observaciones' => $valueIndice->indice_tabla1->observaciones,
                        'user_creator_id' => Auth::user()->id,
                    ]);
                } catch (\QueryException $e) {
                    Log::error('QueryException', ['Exception' => $e]);
                    $error = trans('mensajes.error.insert_db');
                    Session::flash('error', $error);
                    $jsonResponse['status'] = false;
                    $jsonResponse['message'] = [$error];

                    return response()->json($jsonResponse);
                }

                $indice = $valueIndice->indice_tabla1;
                $publicacion_inicial = $valorFirst->publicacion;
                $fecha_inicio = DateTime::createFromFormat(
                    'd/m/Y',
                    '01/' . $publicacion_inicial->mes . '/' . $publicacion_inicial->anio
                );
                $indice->fecha_inicio = $fecha_inicio->format('Y-m-d');
                $indice->save();
            }

            // Recorre cada uno de los valores
            foreach ($valores as $valueValorIndice) {

                try {
                    ValorIndicePublicado::create([
                        'tabla_indices_id' => $tabla_indices_id,
                        'valor' => '' . $valueValorIndice->valor,
                        'variacion' => '' . $valueValorIndice->variacion,
                        'user_creator_id' => $user_id,
                        'publicacion_id' => $valueValorIndice->publicacion_id,
                    ]);
                } catch (\QueryException $e) {
                    Log::error('QueryException', ['Exception' => $e]);
                    $error = trans('mensajes.error.insert_db');
                    Session::flash('error', $error);
                    $response['status'] = false;
                    $response['message'] = [$error];

                    return $response;
                }
            }

            IndiceAPublicar::whereTablaIndicesId($tabla_indices_id)->delete();
        }

        // Inicializa el array...
        $inserts = [];
        $publicados = ValorIndicePublicado::wherePublicacionId($id)->get();
        $publicados = $publicados->keyBy('tabla_indices_id');

        $instancia = InstanciaPublicacionIndice::wherePublicacionId($id)
            ->orderBy('batch', 'desc')
            ->first();

        // Último batch
        $batch = $instancia->batch;

        // Valores correpondientes al ultimo batch
        $valores = ValorIndice::wherePublicacionId($id)
            ->whereBatch($batch)
            ->get();

        // Publico los indices
        $now = Carbon::now();

        // Procesa cada uno de los valores de indices
        foreach ($valores as $valor) {

            $indice_tabla1 = $valor->indice_tabla1;

            // Primera publicacion del valor
            // Ejemplo, si tenemos un indice que se creo en 01/2020 pero va para atras hasta el 11/2019
            // obtenemos la publicacion del 11/2019
            $primer_valor = $indice_tabla1
                ->valores_publicados()
                ->orderBy("tabla_indices_id", "asc")
                ->first();

            if ($primer_valor) {
                $primer_publicacion = $primer_valor->publicacion()->first();
            } else {
                $primer_publicacion = $publicacion;
            }

            // Si el indice fue modificado se crea un nuevo periodo
            if ($indice_tabla1->modificado) {
                // termino periodo anterior
                $periodo_actual = $indice_tabla1->periodo_actual;

                if ($periodo_actual != null) {
                    $periodo_actual->publicacion_fin_id = $id;

                    try {
                        $periodo_actual->save();
                    } catch (QueryException $e) {
                        Log::error('QueryException', ['Exception' => $e]);

                        return $this->responseJsonError([trans('mensajes.error.insert_db')]);
                    }
                } else {
                    $fecha_inicio = DateTime::createFromFormat('d/m/Y', '01/' . $primer_publicacion->mes . '/' . $primer_publicacion->anio);
                    $indice_tabla1->fecha_inicio = $fecha_inicio->format('Y-m-d');
                    $indice_tabla1->save();
                }

                // Inicio un nuevo periodo
                try {
                    $nuevo_periodo = PeriodoIndiceTabla1::create([
                        'tabla_indices_id' => $indice_tabla1->id,
                        'publicacion_inicio_id' => $primer_publicacion->id,
                        'nro' => $indice_tabla1->nro,
                        'clasificacion_id' => $indice_tabla1->clasificacion_id,
                        'nombre' => $indice_tabla1->nombre,
                        'aplicacion' => $indice_tabla1->aplicacion,
                        'observaciones' => $indice_tabla1->observaciones,
                        'user_creator_id' => Auth::user()->id,
                    ]);
                    $nuevo_periodo->save();
                } catch (\QueryException $e) {
                    Log::error('QueryException', ['Exception' => $e]);

                    return $this->responseJsonError([trans('mensajes.error.insert_db')]);
                }

                // Quita la bandera de modificado
                $indice_tabla1->modificado = false;

                try {
                    $indice_tabla1->save();
                } catch (\QueryException $e) {
                    Log::error('QueryException', ['Exception' => $e]);

                    return $this->responseJsonError([trans('mensajes.error.insert_db')]);
                }
            }


            // Verifica que no haya un valor previamente guardado
            $publicado = $publicados->get($valor->tabla_indices_id);

            if ($publicado) {
                $publicado->valor = (string) $valor->valor;
                $publicado->variacion = (string) $valor->variacion;
                $publicado->save();

                continue;
            }

            // Prepara el valor para ser publicado
            $inserts[] = [
                'tabla_indices_id' => $valor->tabla_indices_id,
                'valor' => (string) $valor->valor,
                'variacion' => (string) $valor->variacion,
                'user_creator_id' => $user_id,
                'publicacion_id' => $publicacion->id,
                'updated_at' => $now,
                'created_at' => $now,
            ];
        }

        // Publica valores
        try {
            ValorIndicePublicado::insert($inserts);
        } catch (\QueryException $e) {
            Log::error('QueryException', ['Exception' => $e]);
            $error = trans('mensajes.error.insert_db');

            Session::flash('error', $error);
            $response['status'] = false;
            $response['message'] = [$error];

            return $response;
        }

        // Registra la publicación de los indices
        try {
            InstanciaPublicacionIndice::create([
                'estado' => 'publicar',
                'observaciones' => '',
                'publicacion_id' => $publicacion->id,
                'batch' => $batch,
                'user_creator_id' => Auth::user()->id
            ]);
        } catch (QueryException $e) {
            Log::error('QueryException', ['Exception' => $e]);

            return $this->responseJsonError([trans('mensajes.error.insert_db')]);
        }

        // Guarda la publicación
        $publicacion->user_publicador_id = $user_id;
        $publicacion->fecha_publicacion = date("Y-m-d H:i:s");
        $publicacion->save();

        // Pone en cola
        $tomorrow = Carbon::tomorrow();
        $tomorrow->addHours(config('custom.hora_proceso'))
            ->addMinutes(config('custom.min_proceso'));

        $this->dispatch((new CalculoVariacionEnPublicacion())
            ->delay($tomorrow)
            ->onQueue('calculos_variacion'));
        $publicacion->sendAprobarDesaprobarNotification('publicados');

        // Mensaje de salida
        $message = trans('publicaciones.mensajes.publicada');

        $response = [
            'status' => true,
            'batch' => $batch,
            'message' => $message,
        ];
        Session::flash('success', $message);

        return $response;
    }

    /**
     * @param  int  $id
     * @param  array  $request
     */
    public function rechazar($id, Request $request)
    {
        $publicacion = PublicacionIndice::find($id);
        $publicacion->publicado = 1;
        $user_id = Auth::user()->id;
        $input = $request->all();

        $instancia = InstanciaPublicacionIndice::wherePublicacionId($id)->orderBy('batch', 'desc')->first();
        $batch = $instancia->batch;
        try {
            InstanciaPublicacionIndice::create([
                'estado' => 'rechazar',
                'observaciones' => $input['observaciones'],
                'publicacion_id' => $publicacion->id,
                'batch' => $batch, 'user_creator_id' => Auth::user()->id
            ]);
        } catch (QueryException $e) {
            Log::error('QueryException', ['Exception' => $e]);
            $mensaje = trans('mensajes.error.insert_db');

            Session::flash('error', $mensaje);
            $jsonResponse['status'] = false;
            $jsonResponse['message'] = [$mensaje];

            return response()->json($jsonResponse);
        }

        $publicacion->sendAprobarDesaprobarNotification('rechazados');

        $jsonResponse['status'] = true;
        $jsonResponse['refresh'] = true;
        $jsonResponse['batch'] = $batch;
        $message = trans('publicaciones.mensajes.rechazada');

        Session::flash('success', $message);
        $jsonResponse['message'] = [$message];

        return response()->json($jsonResponse);
    }

    /**
     * @param  int  $id
     */
    public function show($id)
    {
        $publicacion = PublicacionIndice::findOrFail($id);
        $valores = $publicacion->valores_por_categoria;
        $moneda = Moneda::findOrFail($publicacion->moneda_id);

        if ($moneda) {
            $valores_filtrados = array_filter($valores, function ($var) use ($moneda) {
                return $var['moneda_id'] == $moneda->id;
            });
        } else {
            $valores_filtrados = $valores;
        }

        $categorias = Clasificacion::select('id', 'subcategoria', 'categoria')
            ->get()
            ->keyBy('id')
            ->map(function ($categoria, $key) {
                return [
                    'categoria' => $categoria->categoria,
                    'subcategoria' => $categoria->subcategoria,
                ];
            });

        $valores_por_categoria = [];

        foreach ($valores_filtrados as $keyMoneda => $valueMoneda) {
            $valores_por_categoria[$keyMoneda]['moneda_id'] = $valueMoneda['moneda_id'];
            $valores_por_categoria[$keyMoneda]['moneda_key'] = $valueMoneda['moneda_key'];
            $valores_por_categoria[$keyMoneda]['moneda'] = $valueMoneda['moneda'];
            $valores_por_categoria[$keyMoneda]['valores'] = [];

            foreach ($valueMoneda['valores'] as $keyValorPorCat => $valueValorPorCat) {
                $categoria = $categorias[$keyValorPorCat]['categoria'];
                $subcategoria = $categorias[$keyValorPorCat]['subcategoria'];

                foreach ($valueValorPorCat as $valueValor) {
                    $valores_por_categoria[$keyMoneda]['valores'][$categoria][$subcategoria][] = $valueValor;
                }
            }
        }

        return view('publicaciones.show', compact('publicacion', 'valores_por_categoria'));
    }

    /**
     * @param  int id
     */
    public function historial($id)
    {
        $publicacion = PublicacionIndice::find($id);
        $instancias = $publicacion->instancias;

        $jsonResponse = View::make('publicaciones.historial', compact('instancias'))->render();
        return response()->json($jsonResponse);
    }

    public function reporteIndices()
    {
        try{
            $monedas = Moneda::all();
            $moneda = Moneda::whereSimbolo('ARS')->first();

            if ($moneda == null) {
                $moneda = Moneda::first();
            }

            $moneda_id = $moneda->id;

            $anios_eloquent = PublicacionIndice::select('anio')
                ->wherePublicado(1)
                ->whereMonedaId($moneda_id)
                ->distinct('anio')
                ->orderBy('anio', 'desc')
                ->get();

            $anios = [];

            foreach ($anios_eloquent as $keyAnio => $valueAnio) {
                $anios[$valueAnio->anio] = $valueAnio->anio;
            }

            $selected_anio = max($anios);            
            
            $data = $this->getHtmlTablareporteIndices($selected_anio, $moneda_id)->getData();
            if($data->status == 200){
                $html_tabla = $data->view;
                       
                return view(
                    'publicaciones.reportes.index',
                    compact('anios', 'html_tabla', 'selected_anio', 'monedas', 'moneda_id', 'moneda')
                );
            }
            else{
                return response()->json([
                    'data' => $data->data,
                    'status' => $data->status]);
            }
            
            
        }
        catch(\Exception $ex) {            
            return response()->json([
                'data' => $ex->getMessage().' - Line'.$ex->getLine(),
                'status' => 500]);

        }
    }

    /**
     * @param  int  $anio
     * @param  int  $moneda_id
     */
    public function getHtmlTablareporteIndices($anio, $moneda_id)
    {
        try{            
            $publicacion_primer_mes = PublicacionIndice::whereAnio($anio)
                ->whereMonedaId($moneda_id)
                ->wherePublicado(1)
                ->orderBy('mes')
                ->first()->id;

            $publicacion_ultimo_mes = PublicacionIndice::whereAnio($anio)
                ->whereMonedaId($moneda_id)
                ->wherePublicado(1)
                ->orderBy('mes', 'desc')->first()->id;
                
            $indices = IndiceTabla1::select(
                'id',
                'nro',
                'fuente_id',
                'clasificacion_id',
                'nombre',
                'observaciones'
            )
                ->whereMonedaId($moneda_id)
                ->get()
                ->filter(function ($indice) use ($publicacion_primer_mes, $publicacion_ultimo_mes) {
                    return $indice->periodo_actual != null &&
                        ($indice->periodo_actual->publicacion_inicio_id <= $publicacion_ultimo_mes) &&
                        ($indice->periodo_actual->publicacion_fin_id == null ||
                            $indice->periodo_actual->publicacion_fin_id > $publicacion_primer_mes);
                })
                ->sortBy(function ($valor, $key) {
                    return $valor->nro;
                }, SORT_NATURAL, false);
            
            $categorias = array_keys($indices->groupBy('clasificacion_id')->toArray());

            $ids_publicaciones_scalar = PublicacionIndice::whereAnio($anio)
                ->whereMonedaId($moneda_id)
                ->orderBy('mes')
                ->pluck('mes', 'id')
                ->toArray();

            $ids_publicaciones_estado = PublicacionIndice::whereAnio($anio)
                ->whereMonedaId($moneda_id)
                ->orderBy('mes')
                ->pluck('publicado', 'id')
                ->toArray();

            $ids_publicaciones_scalar2 = [];
            foreach ($ids_publicaciones_scalar as $keyScalar => $valueScalar) {
                $ids_publicaciones_scalar2[$keyScalar] = $keyScalar;
            }            
            foreach ($indices as $keyIndice => $valueIndice) {
                $valores_eloquent = ValorIndicePublicado::select(
                    'valor',
                    'publicacion_id',
                    'tabla_indices_id'
                )->whereTablaIndicesId($valueIndice->id)->whereIn(
                    'publicacion_id',
                    $ids_publicaciones_scalar2
                )->get();
               
                $valores = [];
                foreach ($valores_eloquent as $keyVal => $valueVal) {
                    $valores[$valueVal->publicacion_id] = $valueVal->valor_show;
                }

                $valores_temp = [];
                foreach ($valores as $keyVal => $valueVal) {
                    $key = array_search($keyVal, $ids_publicaciones_scalar2);
                    $valores_temp[$key] = $valueVal;
                }

                $valueIndice->valores = $valores_temp;
            }
            

            $indices_categorizados = [];
            foreach ($indices as $valueValor) {
                $valueValor->clasificacion_id = $valueValor->clasificacion_id;
                $indices_categorizados[$valueValor->clasificacion_id][] = $valueValor;
            }

            $categorias_eloquent = Clasificacion::whereIn('id', $categorias)
                ->get()
                ->sortBy('subcategoria')
                ->sortBy('categoria')
                ->groupBy('categoria')
                ->transform(function ($item, $k) {
                    return $item->groupBy('subcategoria');
                });

            $valores_por_categoria = [];

            foreach ($categorias_eloquent as $keyCategoria => $valueCategoria) {
                $valores_por_categoria[$keyCategoria] = [];

                foreach ($valueCategoria as $keySubCategoria => $valueSubCategoria) {
                    if (isset($indices_categorizados[$valueSubCategoria[0]->id])) {
                        $valores_por_categoria[$keyCategoria][$keySubCategoria][0] = $indices_categorizados[$valueSubCategoria[0]->id];
                    }
                }
            }

            $ids_publicaciones = [];
            foreach ($ids_publicaciones_scalar as $key => $val) {
                $ids_publicaciones[$key]['mes'] = $val;
                $ids_publicaciones[$key]['publicado'] = $ids_publicaciones_estado[$key];
            }
    
            $jsonResponse['moneda_id'] = $moneda_id;
            $jsonResponse['moneda'] = Moneda::find($moneda_id)->nombre_simbolo;
            
            $jsonResponse['view'] = View::make(
                'publicaciones.reportes.tabla',
                compact('ids_publicaciones', 'valores_por_categoria')
            )->render();
            $jsonResponse['status'] = 200;
            return response()->json($jsonResponse);
        }
        catch(\Exception $ex) {
            report($ex);
            return response()->json([
                'data' => $ex->getMessage().' - Line'.$ex->getLine(),
                'status' => 500]);
        }
        
    }

    public function fuentesIndices()
    {
        $periodo_1 = PublicacionIndice::first()->id;

        $periodos = PeriodoIndiceTabla1::orderBy('publicacion_fin_id')->get()->pluck('id', 'publicacion_fin_id');

        $periodos_inicio = PeriodoIndiceTabla1::orderBy('publicacion_fin_id')->get()->pluck(
            'id',
            'publicacion_inicio_id'
        );

        foreach ($periodos_inicio as $keyPerInicio => $valuePerInicio) {
            $periodos[$keyPerInicio] = $periodos_inicio[$keyPerInicio];
        }

        unset($periodos[null]);
        $periodos_array = $periodos->all();
        ksort($periodos_array);

        $primera = array_reverse($periodos_array)[sizeof($periodos_array) - 1];
        foreach ($periodos_array as $key => $value) {
            if ($value == $primera && $key > 1) {
                unset($periodos_array[$key]);
            }
        }

        $periodos = collect($periodos_array);
        $i = 0;
        foreach ($periodos as $keyPer => $valuePer) {
            if (!isset($anios)) {
                $anios[$i]['show'] = PublicacionIndice::find($periodo_1)->mes_anio . ' - ' . PublicacionIndice::find($keyPer)->mes_anio;
                $anios[$i]['de'] = $periodo_1;
                $anios[$i]['a'] = $keyPer;

                $anios[$i + 1]['show'] = PublicacionIndice::find($keyPer)->mes_anio;
                $anios[$i + 1]['de'] = $keyPer;
            } else {
                $anios[$i]['show'] = $anios[$i]['show'] . ' - ' . PublicacionIndice::find($keyPer)->mes_anio;
                $anios[$i]['a'] = $keyPer;

                $anios[$i + 1]['show'] = PublicacionIndice::find($keyPer)->mes_anio;
                $anios[$i + 1]['de'] = $keyPer;
            }

            $i++;
        }
        if (!isset($keyPer)) {
            $keyPer = $periodo_1;
        }

        $anios[$i]['show'] = PublicacionIndice::find($keyPer)->mes_anio . ' - ' . trans('index.actualidad');
        $anios[$i]['de'] = $keyPer;
        $anios[$i]['a'] = 'actualidad';

        foreach ($anios as $keyAnio => $valueAnio) {
            if ($valueAnio['de'] == $valueAnio['a']) {
                unset($anios[$keyAnio]);
            }
        }

        if (!isset($anios[0])) {
            $first_key = key($anios);
            $anios[0] = reset($anios);
            unset($anios[$first_key]);
        }
        // $selected_anio = $anios[sizeof($anios) - 1];
        $selected_anio = end($anios);

        $monedas = Moneda::all();
        $moneda = Moneda::whereSimbolo('ARS')->first();
        if ($moneda == null) {
            $moneda = Moneda::first();
        }

        $moneda_id = $moneda->id;

        $data = $this->getHtmlTablafuentesIndices($selected_anio['de'], $selected_anio['a'], $moneda_id)->getData();
        $html_tabla = $data->view;

        $selected = array_key_last($anios);
        return view(
            'publicaciones.fuentes.index',
            compact('anios', 'html_tabla', 'selected', 'monedas', 'moneda_id', 'moneda')
        );
    }

    /**
     * @param  int  $de
     * @param  int  $a
     * @param  int  $moneda_id
     */
    public function getHtmlTablafuentesIndices($de, $a, $moneda_id)
    {
        if ($a == 'actualidad') {
            $indices = PeriodoIndiceTabla1::select(
                'ind_indices_periodos.id',
                'ind_indices_periodos.tabla_indices_id',
                'ind_indices_periodos.nro',
                'ind_tabla_indices.fuente_id',
                'ind_indices_periodos.clasificacion_id',
                'ind_indices_periodos.nombre',
                'ind_indices_periodos.observaciones'
            )->join(
                'ind_tabla_indices',
                'ind_tabla_indices.id',
                '=',
                'ind_indices_periodos.tabla_indices_id'
            )->where(
                'ind_tabla_indices.moneda_id',
                $moneda_id
            )->where(function ($query) use ($de) {
                $query->where('publicacion_inicio_id', '>=', $de)->orWhere('publicacion_fin_id', '=', null);
            })->get()->sortBy(function ($valor, $key) {
                return $valor->nro;
            }, SORT_NATURAL, false);
        } else {
            $indices = PeriodoIndiceTabla1::select(
                'ind_indices_periodos.id',
                'ind_indices_periodos.tabla_indices_id',
                'ind_indices_periodos.nro',
                'ind_tabla_indices.fuente_id',
                'ind_indices_periodos.clasificacion_id',
                'ind_indices_periodos.nombre',
                'ind_indices_periodos.observaciones'
            )->join(
                'ind_tabla_indices',
                'ind_tabla_indices.id',
                '=',
                'ind_indices_periodos.tabla_indices_id'
            )->where(
                'ind_tabla_indices.moneda_id',
                $moneda_id
            )->where(function ($query) use ($de, $a) {
                $query->where(function ($subquery1) use ($de, $a) {
                    $subquery1->where('publicacion_inicio_id', '<=', $de)->where('publicacion_fin_id', '=', null);
                })->orWhere(function ($subquery2) use ($de, $a) {
                    $subquery2->where('publicacion_inicio_id', '<=', $de)->where('publicacion_fin_id', '<=', $a);
                });
            })->get()->sortBy(function ($valor, $key) {
                return $valor->nro;
            }, SORT_NATURAL, false);
        }

        $categorias = array_keys($indices->groupBy('clasificacion_id')->toArray());
        $indices_categorizados = [];
        foreach ($indices as $keyValor => $valueValor) {
            $valueValor->clasificacion_id = $valueValor->clasificacion_id;
            $indices_categorizados[$valueValor->clasificacion_id][] = $valueValor;
        }

        $categorias_eloquent = Clasificacion::whereIn(
            'id',
            $categorias
        )->get()->sortBy('subcategoria')->sortBy('categoria')->groupBy('categoria')->transform(function (
            $item,
            $k
        ) {
            return $item->groupBy('subcategoria');
        });

        $valores_por_categoria = [];
        foreach ($categorias_eloquent as $keyCategoria => $valueCategoria) {
            $valores_por_categoria[$keyCategoria] = [];
            foreach ($valueCategoria as $keySubCategoria => $valueSubCategoria) {

                if (isset($indices_categorizados[$valueSubCategoria[0]->id])) {
                    $valores_por_categoria[$keyCategoria][$keySubCategoria][0] = $indices_categorizados[$valueSubCategoria[0]->id];
                }
            }
        }


        $jsonResponse['moneda_id'] = $moneda_id;
        $jsonResponse['moneda'] = Moneda::find($moneda_id)->nombre_simbolo;
        $jsonResponse['view'] = View::make('publicaciones.fuentes.tabla', compact('valores_por_categoria'))->render();

        return response()->json($jsonResponse);
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     */
    public function exportar(Request $request)
    {
        $input = $request->all();
        $filtro = $input['excel_input'];
        $publicaciones = PublicacionIndice::orderBy('anio', 'desc')->orderBy('mes', 'desc')->get();

        $publicaciones = $publicaciones->map(function ($publicacion, $key) {
            if ($publicacion->publicado) {
                $arr[trans('index.publicado')] = trans('index.si');
                $arr['unset'] = trans('index.publicado');
            } else {
                $arr[trans('index.publicado')] = trans('index.no');
                $arr['unset'] = trans('index.no_publicado');
            }

            $arr[trans('forms.estado')] = $publicacion->estado_nombre_color['nombre'];
            $arr[trans('forms.mes_indice')] = $publicacion->mes_anio;
            $arr[trans('forms.user_publicador')] = $publicacion->publicador_nombre_apellido;
            $arr[trans('forms.fecha_publicacion')] = $publicacion->fecha_publicacion;
            return $arr;
        });

        return $this->toExcel(trans('forms.publicaciones'), $this->filtrarExportacion($publicaciones, $filtro));
    }


    /**
     * @param  string  $de
     * @param  string  $a
     * @param  int  $moneda_id
     */
    public function exportarFuentes($de, $a, $moneda_id)
    {
        if ($a == 'actualidad') {
            $indices = PeriodoIndiceTabla1::select(
                'ind_indices_periodos.id',
                'ind_indices_periodos.tabla_indices_id',
                'ind_indices_periodos.nro',
                'ind_tabla_indices.fuente_id',
                'ind_indices_periodos.clasificacion_id',
                'ind_indices_periodos.nombre',
                'ind_indices_periodos.observaciones'
            )->join(
                'ind_tabla_indices',
                'ind_tabla_indices.id',
                '=',
                'ind_indices_periodos.tabla_indices_id'
            )->where(
                'ind_tabla_indices.moneda_id',
                $moneda_id
            )->where(function ($query) use ($de) {
                $query->where('publicacion_inicio_id', '>=', $de)->orWhere('publicacion_fin_id', '=', null);
            })->get()->sortBy(function ($valor, $key) {
                return $valor->nro;
            }, SORT_NATURAL, false);
        } else {
            $indices = PeriodoIndiceTabla1::select(
                'ind_indices_periodos.id',
                'ind_indices_periodos.tabla_indices_id',
                'ind_indices_periodos.nro',
                'ind_tabla_indices.fuente_id',
                'ind_indices_periodos.clasificacion_id',
                'ind_indices_periodos.nombre',
                'ind_indices_periodos.observaciones'
            )->join(
                'ind_tabla_indices',
                'ind_tabla_indices.id',
                '=',
                'ind_indices_periodos.tabla_indices_id'
            )->where(
                'ind_tabla_indices.moneda_id',
                $moneda_id
            )->where(function ($query) use ($de, $a) {
                $query->where(function ($subquery1) use ($de, $a) {
                    $subquery1->where('publicacion_inicio_id', '<=', $de)->where('publicacion_fin_id', '=', null);
                })->orWhere(function ($subquery2) use ($de, $a) {
                    $subquery2->where('publicacion_inicio_id', '<=', $de)->where('publicacion_fin_id', '<=', $a);
                });
            })->get()->sortBy(function ($valor, $key) {
                return $valor->nro;
            }, SORT_NATURAL, false);
        }

        $categorias = array_keys($indices->groupBy('clasificacion_id')->toArray());

        $indices_categorizados = [];
        foreach ($indices as $keyValor => $valueValor) {
            $valueValor->clasificacion_id = $valueValor->clasificacion_id;
            $indices_categorizados[$valueValor->clasificacion_id][] = $valueValor;
        }

        $categorias_eloquent = Clasificacion::whereIn(
            'id',
            $categorias
        )->get()->sortBy('subcategoria')->sortBy('categoria')->groupBy('categoria')->transform(function (
            $item,
            $k
        ) {
            return $item->groupBy('subcategoria');
        });

        $valores_por_categoria = [];
        foreach ($categorias_eloquent as $keyCategoria => $valueCategoria) {
            $valores_por_categoria[$keyCategoria] = [];
            foreach ($valueCategoria as $keySubCategoria => $valueSubCategoria) {

                if (isset($indices_categorizados[$valueSubCategoria[0]->id])) {
                    $valores_por_categoria[$keyCategoria][$keySubCategoria][0] = $indices_categorizados[$valueSubCategoria[0]->id];
                }
            }
        }

        $width = \PHPExcel_Cell::stringFromColumnIndex(5);

        $cortes['cat'] = [];
        $cortes['sub'] = [];
        // El primero
        $cortes['cat'][] = 1;
        $actual = 1;
        $inicio = true;
        $nros = [];
        $elprimero = true;
        foreach ($categorias_eloquent as $keyCategoria => $valueCategoria) {
            $valores_por_categoria[$keyCategoria] = [];

            // uno propio + otro previo
            $next = 2;
            if (!$inicio) {
                $actual = $actual + 2;
            }

            if ($inicio) {
                $inicio = !$inicio;
            }

            $sub = 0;
            foreach ($valueCategoria as $keySubCategoria => $valueSubCategoria) {
                if ($valueSubCategoria[0]->subcategoria == 'N/A') {
                    $next = $next + 2;
                    $actual = $actual + 2;
                } else {
                    $next = $next + 4;
                    $cortes['sub'][] = $actual + 2;
                    $actual = $actual + 4;
                }


                if (isset($indices_categorizados[$valueSubCategoria[0]->id])) {
                    $valores_por_categoria[$keyCategoria][$keySubCategoria][0] = $indices_categorizados[$valueSubCategoria[0]->id];
                    if ($valueSubCategoria[0]->subcategoria == 'N/A') {
                        $inicio_nros = $actual + 1;
                        if ($elprimero) {
                            $elprimero = false;
                            $nros_count = $inicio_nros;
                        } else {
                            $nros_count = $inicio_nros;
                        }
                    } else {
                        $inicio_nros = $cortes['sub'][sizeof($cortes['sub']) - 1];
                        $nros_count = $inicio_nros + 3;
                    }
                    $next = $next + sizeof($indices_categorizados[$valueSubCategoria[0]->id]);
                    $actual = $actual + sizeof($indices_categorizados[$valueSubCategoria[0]->id]);

                    foreach ($indices_categorizados[$valueSubCategoria[0]->id] as $key => $val) {
                        $nros[$nros_count] = $val->nro;
                        $nros_count++;
                    }
                }
            }
            $cortes['cat'][] = $cortes['cat'][sizeof($cortes['cat']) - 1] + $next;
        }
        unset($cortes['cat'][sizeof($cortes['cat']) - 1]);

        $titulo = trans('index.reporte_indices_fuentes');

        Excel::create($titulo, function ($excel) use ($valores_por_categoria, $titulo, $width, $cortes, $nros) {
            $excel->sheet($titulo, function ($sheet) use ($valores_por_categoria, $width, $cortes, $nros) {
                $isExcel = true;
                $sheet->loadView('publicaciones.fuentes.tabla', compact('valores_por_categoria', 'isExcel'));

                foreach ($cortes['cat'] as $key => $value) {
                    $sheet->mergeCells('A' . $value . ':' . $width . $value);
                    $sheet->cells('A' . $value . ':' . $width . $value, function ($cells) {
                        $cells->setBackground('#999999');
                    });
                }

                // foreach ($cortes['sub'] as $key => $value) {
                //   $sheet->mergeCells('A' . $value .':' . $width . $value);
                //   $sheet->cells('A' . $value .':' . $width . $value, function ($cells) {
                //     $cells->setBackground('#f2f2f2');
                //   });
                // }

                // Para que tome string y no transforme a float
                foreach ($nros as $key => $value) {
                    $sheet->setValueOfCell('' . $value, 'A', $key);
                }

                $sheet->setOrientation('landscape');
            });
        })->store('xlsx', storage_path('excel/exports'));

        return Response::json(array(
            'href' => '/excel/exports/' . $titulo . '.xlsx',
        ));
    }

    /**
     * @param  int  $anio
     * @param  int  $moneda_id
     */
    public function exportarIndices($anio, $moneda_id)
    {
        $moneda = Moneda::find($moneda_id);

        $publicacion_primer_mes = PublicacionIndice::whereAnio($anio)->orderBy('mes')->first()->id;

        $publicacion_ultimo_mes = PublicacionIndice::whereAnio($anio)->orderBy('mes', 'desc')->first()->id;

        $indices = IndiceTabla1::whereMonedaId($moneda_id)
            ->get()
            ->filter(function ($indice) use ($publicacion_primer_mes, $publicacion_ultimo_mes) {
                return $indice->periodo_actual != null
                    && ($indice->periodo_actual->publicacion_inicio_id <= $publicacion_ultimo_mes)
                    && ($indice->periodo_actual->publicacion_fin_id == null
                        || $indice->periodo_actual->publicacion_fin_id > $publicacion_primer_mes);
            })
            ->sortBy(function ($valor, $key) {
                return $valor->nro;
            }, SORT_NATURAL, false);

        $categorias = array_keys($indices->groupBy('clasificacion_id')->toArray());

        $ids_publicaciones_scalar = PublicacionIndice::whereAnio($anio)->whereMonedaId($moneda_id)->orderBy('mes')->pluck(
            'mes',
            'id'
        )->toArray();

        $ids_publicaciones_estado = PublicacionIndice::whereAnio($anio)->whereMonedaId($moneda_id)->orderBy('mes')->pluck(
            'publicado',
            'id'
        )->toArray();

        $ids_publicaciones_scalar2 = [];
        foreach ($ids_publicaciones_scalar as $keyScalar => $valueScalar) {
            $ids_publicaciones_scalar2[$keyScalar] = $keyScalar;
        }

        foreach ($indices as $keyIndice => $valueIndice) {
            $valores_eloquent = ValorIndicePublicado::select(
                'valor',
                'publicacion_id',
                'tabla_indices_id'
            )->whereTablaIndicesId($valueIndice->id)->whereIn(
                'publicacion_id',
                $ids_publicaciones_scalar2
            )->get();

            $valores = [];
            foreach ($valores_eloquent as $keyVal => $valueVal) {
                $valores[$valueVal->publicacion_id] = $valueVal->valor_show;
            }

            $valores_temp = [];
            foreach ($valores as $keyVal => $valueVal) {
                $key = array_search($keyVal, $ids_publicaciones_scalar2);
                $valores_temp[$key] = $valueVal;
            }

            $valueIndice->valores = $valores_temp;
        }

        $indices_categorizados = [];
        foreach ($indices as $keyValor => $valueValor) {
            $valueValor->clasificacion_id = $valueValor->clasificacion_id;
            $indices_categorizados[$valueValor->clasificacion_id][] = $valueValor;
        }

        $categorias_eloquent = Clasificacion::whereIn(
            'id',
            $categorias
        )->get()->sortBy('subcategoria')->sortBy('categoria')->groupBy('categoria')->transform(function (
            $item,
            $k
        ) {
            return $item->groupBy('subcategoria');
        });

        $excelData['valores_por_categoria'] = [];

        $excelData['cortes']['cat'] = [];
        $excelData['cortes']['sub'] = [];
        // El primero
        $excelData['cortes']['cat'][] = 1;
        $actual = 1;
        $inicio = true;
        $excelData['nros'] = [];
        $elprimero = true;
        foreach ($categorias_eloquent as $keyCategoria => $valueCategoria) {
            $excelData['valores_por_categoria'][$keyCategoria] = [];

            // uno propio + otro previo
            $next = 2;
            if (!$inicio) {
                $actual = $actual + 2;
            }

            if ($inicio) {
                $inicio = !$inicio;
            }

            $sub = 0;
            foreach ($valueCategoria as $keySubCategoria => $valueSubCategoria) {
                if ($valueSubCategoria[0]->subcategoria == 'N/A') {
                    $next = $next + 2;
                    $actual = $actual + 2;
                } else {
                    $next = $next + 4;

                    $excelData['cortes']['sub'][] = $actual + 2;
                    $actual = $actual + 4;
                }

                if (isset($indices_categorizados[$valueSubCategoria[0]->id])) {
                    $excelData['valores_por_categoria'][$keyCategoria][$keySubCategoria][0] = $indices_categorizados[$valueSubCategoria[0]->id];

                    if ($valueSubCategoria[0]->subcategoria == 'N/A') {
                        $inicio_nros = $actual + 1;
                        if ($elprimero) {
                            $elprimero = false;
                            $nros_count = $inicio_nros;
                        } else {
                            $nros_count = $inicio_nros;
                        }
                    } else {
                        $inicio_nros = $excelData['cortes']['sub'][sizeof($excelData['cortes']['sub']) - 1];
                        $nros_count = $inicio_nros + 3;
                    }

                    $next = $next + sizeof($indices_categorizados[$valueSubCategoria[0]->id]);
                    $actual = $actual + sizeof($indices_categorizados[$valueSubCategoria[0]->id]);

                    foreach ($indices_categorizados[$valueSubCategoria[0]->id] as $key => $val) {
                        $excelData['nros'][$nros_count] = $val->nro;
                        $nros_count++;
                    }
                }
            }
            $excelData['cortes']['cat'][] = $excelData['cortes']['cat'][sizeof($excelData['cortes']['cat']) - 1] + $next;
        }
        unset($excelData['cortes']['cat'][sizeof($excelData['cortes']['cat']) - 1]);

        $excelData['ids_publicaciones'] = [];
        foreach ($ids_publicaciones_scalar as $key => $val) {
            $excelData['ids_publicaciones'][$key]['mes'] = $val;
            $excelData['ids_publicaciones'][$key]['publicado'] = $ids_publicaciones_estado[$key];
        }

        $excelData['cant_meses'] = sizeof($excelData['ids_publicaciones']);
        // Columnas de meses + # + nombre - 1 porque la cuenta arranca en 1
        $excelData['width'] = \PHPExcel_Cell::stringFromColumnIndex($excelData['cant_meses'] + 2 - 1);

        $titulo = trans('index.reporte_indices_valores') . ' ' . $moneda->nombre_simbolo;

        $xlsx = Excel::create($titulo . '_' . $anio, function ($excel) use ($titulo, $excelData) {
            $excel->sheet($titulo, function ($sheet) use ($excelData) {
                $isExcel = true;
                $valores_por_categoria = $excelData['valores_por_categoria'];
                $ids_publicaciones = $excelData['ids_publicaciones'];
                $sheet->loadView(
                    'publicaciones.reportes.tabla',
                    compact('ids_publicaciones', 'valores_por_categoria', 'isExcel')
                );

                for ($i = 0; $i <= ($excelData['cant_meses'] + 1); $i++) {
                    // Letra de la columna
                    $cell_name = \PHPExcel_Cell::stringFromColumnIndex($i);
                    if ($i == 0) {
                        // A: nro
                        $sheet->setWidth($cell_name, 8);
                        $sheet->getStyle($cell_name)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    } else {
                        if ($i == 1) {
                            // B: nombre
                            $sheet->setWidth($cell_name, 50);
                        } else {
                            $sheet->setWidth($cell_name, 10);
                            $sheet->getStyle($cell_name)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                        }
                    }
                }

                foreach ($excelData['cortes']['cat'] as $key => $value) {
                    $sheet->mergeCells('A' . $value . ':' . $excelData['width'] . $value);
                    $sheet->cells('A' . $value . ':' . $excelData['width'] . $value, function ($cell) {
                        $cell->setBackground('#999999');
                    });
                }

                foreach ($excelData['cortes']['sub'] as $key => $value) {
                    $sheet->mergeCells('A' . $value . ':' . $excelData['width'] . $value);
                    $sheet->cells('A' . $value . ':' . $excelData['width'] . $value, function ($cells) {
                        $cells->setBackground('#f2f2f2');
                    });
                }

                // Para que tome string y no transforme a float
                foreach ($excelData['nros'] as $key => $value) {
                    $sheet->setValueOfCell('' . $value, 'A', $key);
                }

                $sheet->setOrientation('landscape');
            });
        })->store('xlsx', storage_path('excel/exports'));

        return Response::json(array(
            'href' => '/excel/exports/' . $titulo . '_' . $anio . '.xlsx',
        ));
    }

    /**
     * @param  int  $id
     */
    public function exportarEdicion($id)
    {
        $publicacion = PublicacionIndice::findOrFail($id);

        $valores = $publicacion->valores_por_categoria;

        $es_borrador = $publicacion->inputs_editables;

        $categorias_eloquent = Clasificacion::select(
            'id',
            'subcategoria',
            'categoria'
        )->get()->groupBy('categoria')->transform(function ($item, $k) {
            return $item->groupBy('subcategoria');
        });
        $excelData = [];
        foreach ($valores as $keyDataValor => $valDataValor) {
            $mapeo_categorias = [];
            $moneda_id = $valDataValor['moneda_id'];
            $excelData[$moneda_id]['cortes']['cat'] = [];
            $excelData[$moneda_id]['cortes']['sub'] = [];

            // El primero
            $excelData[$moneda_id]['cortes']['cat'][] = 1;
            $actual = 1;
            $inicio = true;
            $excelData[$moneda_id]['nros'] = [];
            $elprimero = true;
            $cant_sub = 0;

            $valores_clasificados = [];
            foreach ($valDataValor['valores'] as $keyValor => $valueValor) {
                $valores_clasificados[$valueValor[0]->indice_tabla1->clasificacion->categoria][$keyValor] = $valDataValor['valores'][$keyValor];
            }

            $valores_clasificados_2 = [];
            foreach ($valores_clasificados as $keyValorClasificado => $valueValorClasificado) {
                foreach ($valueValorClasificado as $keySubCategoria => $valueSubCategoria) {
                    $valores_clasificados_2[$keySubCategoria] = $valueSubCategoria;
                }
            }

            $valDataValor['valores'] = $valores_clasificados_2;
            $orden_categorias = array_keys($valDataValor['valores']);

            foreach ($orden_categorias as $keyOrden => $valueOrden) {
                $keyCategoria = null;
                $valueCategoria = null;

                $keySubCategoria = null;
                $valueSubCategoria = null;
                foreach ($categorias_eloquent as $keyCategoriaLoop => $valueCategoriaLoop) {
                    foreach ($valueCategoriaLoop as $keySubCategoriaLoop => $valueSubCategoriaLoop) {
                        if ($valueSubCategoriaLoop[0]->id == $valueOrden) {
                            $keyCategoria = $keyCategoriaLoop;
                            $valueCategoria = $valueCategoriaLoop;

                            $keySubCategoria = $keySubCategoriaLoop;
                            $valueSubCategoria = $valueSubCategoriaLoop;
                        }
                    }
                }

                if ($valueCategoria != null) {
                    $excelData[$moneda_id]['moneda'] = $valDataValor['moneda'];

                    if (!isset($excelData[$moneda_id]['valores_por_categoria'][$keyCategoria])) {
                        $excelData[$moneda_id]['valores_por_categoria'][$keyCategoria] = [];
                    }

                    // uno propio + otro previo
                    $next = 2;
                    if (!$inicio) {
                        $actual = $actual + 2;
                    }

                    if ($inicio) {
                        $inicio = !$inicio;
                    }

                    $sub = 0;

                    if ($valueSubCategoria[0]->subcategoria == 'N/A') {
                        $next = $next + 2;
                        $actual = $actual + 2;
                    } else {
                        $next = $next + 4;

                        $excelData[$moneda_id]['cortes']['sub'][] = $actual + 2;
                        $actual = $actual + 4;
                    }

                    $excelData[$moneda_id]['valores_por_categoria'][$keyCategoria][$keySubCategoria][] = $valDataValor['valores'][$valueSubCategoria[0]->id];

                    if ($valueSubCategoria[0]->subcategoria == 'N/A') {
                        $inicio_nros = $actual + 1;
                        if ($elprimero) {
                            $elprimero = false;
                            $nros_count = $inicio_nros;
                        } else {
                            $nros_count = $inicio_nros;
                        }
                    } else {
                        $inicio_nros = $excelData[$moneda_id]['cortes']['sub'][sizeof($excelData[$moneda_id]['cortes']['sub']) - 1];
                        $nros_count = $inicio_nros + 3;
                        if (!isset($mapeo_categorias[$keyCategoria])) {

                            // if($cant_sub != 0) {
                            $nros_count = $nros_count - (2 * $cant_sub);
                            $excelData[$moneda_id]['cortes']['sub'][sizeof($excelData[$moneda_id]['cortes']['sub']) - 1] = $excelData[$moneda_id]['cortes']['sub'][sizeof($excelData[$moneda_id]['cortes']['sub']) - 1] - (2 * $cant_sub);
                            // }
                            $cant_sub++;
                            $cant_sub = 0;
                        } elseif (isset($mapeo_categorias[$keyCategoria])) {
                            // echo $cant_sub . ' || ';
                            $cant_sub++;
                            $nros_count = $nros_count - (2 * $cant_sub);
                            $excelData[$moneda_id]['cortes']['sub'][sizeof($excelData[$moneda_id]['cortes']['sub']) - 1] = $excelData[$moneda_id]['cortes']['sub'][sizeof($excelData[$moneda_id]['cortes']['sub']) - 1] - (2 * $cant_sub);
                            // $actual = $actual - (2 * $cant_sub);
                        }
                    }

                    $cant_valores = sizeof($valDataValor['valores'][$valueSubCategoria[0]->id]);

                    $next = $next + $cant_valores;
                    $actual = $actual + $cant_valores;

                    // echo 'ANTES:' . $nros_count . ' ';
                    foreach ($valDataValor['valores'][$valueSubCategoria[0]->id] as $keyIndice => $valIndice) {
                        $excelData[$moneda_id]['nros'][$nros_count] = $valIndice->indice_tabla1->nro;
                        $nros_count++;
                    }
                    // echo 'DESPUES:' . $nros_count . ' ';
                    // echo $valueSubCategoria[0]->subcategoria . '<br>';
                }

                if (!isset($mapeo_categorias[$keyCategoria])) {
                    $excelData[$moneda_id]['cortes']['cat'][] = $excelData[$moneda_id]['cortes']['cat'][sizeof($excelData[$moneda_id]['cortes']['cat']) - 1] + $next;
                    $mapeo_categorias[$keyCategoria] = sizeof($excelData[$moneda_id]['cortes']['cat']) - 1;
                } else {
                    $excelData[$moneda_id]['cortes']['cat'][$mapeo_categorias[$keyCategoria]] += $next - 2;
                    for ($i = ($mapeo_categorias[$keyCategoria] + 1); $i < sizeof($excelData[$moneda_id]['cortes']['cat']); $i++) {
                        $excelData[$moneda_id]['cortes']['cat'][$i] += $next - 2;
                    }
                }
            }

            // Saco el ultimo
            unset($excelData[$moneda_id]['cortes']['cat'][sizeof($excelData[$moneda_id]['cortes']['cat']) - 1]);
            // dd($excelData[$moneda_id]['nros']);
        }

        $titulo = trans('index.indices_mensual') . '_' . $this->toKey($publicacion->mes_anio);
        $xlsx = Excel::create($titulo, function ($excel) use ($excelData, $es_borrador, $publicacion) {

            foreach ($excelData as $keyMoneda => $valueMoneda) {
                $valores_por_categoria = $valueMoneda['valores_por_categoria'];
                $moneda_id = $keyMoneda;

                $excel->sheet(
                    $valueMoneda['moneda'],
                    function ($sheet) use ($excelData, $valores_por_categoria, $es_borrador, $publicacion, $moneda_id) {
                        $isExcel = true;
                        $nros = $excelData[$moneda_id]['nros'];
                        $cortes = $excelData[$moneda_id]['cortes'];
                        $sheet->loadView(
                            'publicaciones.tabla_edit',
                            compact('valores_por_categoria', 'publicacion', 'es_borrador', 'isExcel')
                        );

                        $width = 'G';
                        foreach ($cortes['cat'] as $key => $value) {
                            $sheet->mergeCells('A' . $value . ':' . $width . $value);
                            $sheet->cells('A' . $value . ':' . $width . $value, function ($cell) {
                                $cell->setBackground('#999999');
                            });
                        }

                        // foreach ($cortes['sub'] as $key => $value) {
                        //   // $sheet->mergeCells('A' . $value .':' . $width . $value);
                        //   $sheet->cells('A' . $value . ':' . $width . $value, function ($cells) {
                        //     $cells->setBackground('#F2F2F2');
                        //   });
                        // }

                        // Para que tome string y no transforme a float
                        // foreach ($nros as $key => $value) {
                        //   $sheet->setValueOfCell('' . $value, 'A', $key);
                        // }

                        $sheet->setOrientation('landscape');
                    }
                );
            }
        })->store('xlsx', storage_path('excel/exports'));

        return Response::json(array(
            'href' => '/excel/exports/' . $titulo . '.xlsx',
        ));
    }
}
