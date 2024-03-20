<?php

namespace App\Http\Controllers\Contratos;

use App\User;
use CalculoRedeterminacion\CalculoModelExtended;
use CalculoRedeterminacion\Polinomica;
use CalculoRedeterminacion\VariacionMesPolinomica;
use Contratista\Contratista;
use Contrato\Contrato;
use Contrato\ContratoAdjunto;
use Contrato\ContratoMoneda\ContratoMoneda;
use Contrato\EstadoContrato;
use Contrato\InstanciaContrato;
use Contrato\OrganoAprobador;
use Contrato\Plazo;
use Contrato\RepresentanteEby;
use Contrato\TipoContrato;
use CuadroComparativo\CuadroComparativo;
use DateTime;
use DB;
use Hash;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Indice\IndiceTabla1;
use Indice\ValorIndicePublicado;
use Itemizado\Itemizado;
use Itemizado\UnidadMedida;
use Log;
use Redirect;
use Response;
use Storage;
use View;
use Yacyreta\Causante;
use Yacyreta\Moneda;

class ContratosController extends ContratosControllerExtended
{

    public function __construct()
    {
        View::share('ayuda', 'contrato');
        $this->middleware('auth', ['except' => 'logout']);
    }

    //#region Contratos

    /**
     * @param  \Illuminate\Http\Request  $request
     */
    public function index(Request $request)
    {
        $input = $request->all();
        $search_input = '';
        if (isset($input['search_input'])) {
            $search_input = $this->minusculaSinAcentos($input['search_input']);
        }

        $contratos_admin = Auth::user()->contratos_admin;

        // $contratos_admin = $this->ordenar($contratos_admin);

        if ($request->getMethod() == "GET") {
            if ($search_input != '') {
                $contratos_admin = $this->filtrar($contratos_admin, $search_input);
            }
            $contratos = $this->paginateCustom($contratos_admin);
        }
        else {
            $contratos_admin = $this->filtrar($contratos_admin, $search_input);
            $contratos = $this->paginateCustom($contratos_admin, 1);
        }

        $publicados = true;

        return view('contratos.contratos.index', compact('contratos', 'search_input', 'publicados'));
    }

    /**
     * @param  Contrato\Contrato  $contratos
     * @return mixed
     */
    private function ordenar($contratos)
    {
        return $contratos->sortBy(function ($contrato, $key) {
            return $contrato->created_at;
        })->all();
    }

    /**
     * @param  int  $id
     * @param  string  $accion  |nullable
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function verAdenda($id, $accion = null)
    {
        $contrato = Contrato::findOrFail($id);

        if ($contrato->is_adenda)
            return $this->ver($id, $accion);
        else
            return redirect()->route('contratos.index');
    }

    /**
     * @param  int  $id
     * @param  string  $accion  |nullable
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function ver($id, $accion = null)
    {
        // Carga Contrato con todad sus relaciones
        $contrato = Contrato::whereId($id)
          ->with([
              'tipo_contrato',
              'garantias',
              'certificados',
              'representante_eby',
              'contrato_padre',
          ])
          ->firstOrFail();

        // Si es adenda Rrcupera el causante del contrato padre, en caso contraripo lo hace del mismo contrato
        $causante_id = $contrato->is_adenda
          ? $contrato->contrato_padre->causante_id
          : $contrato->causante_id;

        // Comprueba que el usario puede ver a este causante
        if (!Auth::user()->puedeVerCausante($causante_id))
            return redirect()->route('contratos.index');

        // Comprueba que el usuario puede ver esta tipo de cantrato
        if (!Auth::user()->puedeVerTipoContrato($contrato->tipo_contrato))
            return redirect()->route('contratos.index');

        // Obtiene la infomación de estadp
        $contratoIncompleto = $contrato->incompleto;

        // Define variable para los inices
        $indices = new Collection();

        // Define variable para las unidades de medida
        $unidadesMedida = [];

        // Define variable par los responsables
        $responsables = [];

        // Se hay definido un estado verifica que tenga polinomica
        // o itemizado con sus respetivos permisos
        if (
          $contratoIncompleto['status']
          && (
            ($contratoIncompleto['polinomica'] && Auth::user()->can('polinomica-edit'))
            || ($contratoIncompleto['itemizado'] && Auth::user()->can('itemizado-edit'))
          )
        )
        {
            // Todas las monedas
            $ids = ContratoMoneda::select('moneda_id')
              ->whereClaseId($id)
              ->whereClaseType($contrato->getClassName())
              ->get()
              ->pluck('moneda_id', 'moneda_id')
              ->toArray();

            // Si es contrato optiene fercha de oferta de este, en caso contrario lo obtiene de contraro original
            $fecha_oferta = $contrato->is_contrato
              ? $contrato->fecha_oferta
              : $contrato->contrato_original->fecha_oferta;

            // Recuepera los indices desde la fecha de oferta y los agrupa por moneda
            $indices = IndiceTabla1::whereIn('moneda_id', $ids)
              ->where('fecha_inicio', '!=', null)
              ->where('fecha_inicio', '<=', $this->fechaDeA($fecha_oferta, 'd/m/Y', 'Y-m-d'))
              ->get()
              ->groupBy('moneda_id');            

            // Si tiene itemizado recupera unidades de medida y responsables
            if ($contratoIncompleto['itemizado'] && Auth::user()->can('itemizado-edit')) {
                $unidadesMedida = UnidadMedida::getOpciones();
                $responsables = Contratista::getOpciones();
            }
        }

        $opciones['version'] = 'vigente';
        $opciones['visualizacion'] = 'porcentaje';

        $dobleFirma = false;
        $causante = null;
        $firmaAr = null;
        $firmaPy = null;

        if ($causante_id)
            $causante = Causante::find($causante_id);

        if ($causante) {
            $dobleFirma = $causante->doble_firma;
            $firmaAr = $causante->jefe_contrato_ar;
            $firmaPy = $causante->jefe_contrato_py;
        }

        return view('contratos.contratos.show.index', compact(
          'contrato',
          'contratoIncompleto',
          'accion',
          'indices',
          'opciones',
          'unidadesMedida',
          'responsables',
          'dobleFirma',
          'firmaAr',
          'firmaPy'
        ));
    }

    /**
     * @param  int  $id
     * @param  string  $accion  | nullable
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function verEditar($id, $accion = null)
    {
        if ($accion == 'polinomica' && Auth::user()->cant('polinomica-edit')) {
            return redirect()->route('contratos.index')->with(['error' => trans('mensajes.error.contrato_borrador')]);
        }
        else if ($accion == 'itemizado' && Auth::user()->cant('itemizado-manage')) {
            return redirect()->route('contratos.index')->with(['error' => trans('mensajes.error.contrato_borrador')]);
        }
        else if ($accion == 'cronograma' && Auth::user()->cant('cronograma-manage')) {
            return redirect()->route('contratos.index')->with(['error' => trans('mensajes.error.contrato_borrador')]);
        }
        else if ($accion == 'adendaAmpliacion' && Auth::user()->cant('adenda_ampliacion-create')) {
            return redirect()->route('contratos.index')->with(['error' => trans('mensajes.error.contrato_borrador')]);
        }
        else if ($accion == 'adendaCertificacion' && Auth::user()->cant('adenda_certificacion-create')) {
            return redirect()->route('contratos.index')->with(['error' => trans('mensajes.error.contrato_borrador')]);
        }

        return $this->ver($id, $accion);
    }

    /**
     * @param  Illuminate\Database\Eloquent\Collection  $contratos_admin
     * @param  string  $input_lower
     */
    private function filtrar($contratos_admin, $input_lower)
    {
        if ($input_lower == '') {
            return $contratos_admin;
        }

        $no_redetermina = false;
        $redetermina = false;

        if ($this->stringContains(trans('contratos.no_redetermina'), $input_lower)) {
            $no_redetermina = true;
        }

        if ($this->stringContains(trans('contratos.redetermina'), $input_lower)) {
            $redetermina = true;
        }

        $empalme = false;

        if ($this->stringContains(trans('contratos.empalme'), $input_lower)) {
            $empalme = true;
        }

        return $contratos_admin->filter(function ($contrato) use (
          $input_lower,
          $no_redetermina,
          $redetermina,
          $empalme
        ) {
            if ($empalme && $contrato->empalme) {
                return true;
            }

            if ($contrato->tiene_contratos_monedas) {
                foreach ($contrato->contratos_monedas as $keyContratoMoneda => $valueContratoMoneda) {
                    if ($this->stringContains($valueContratoMoneda->moneda->nombre_simbolo, $input_lower)) {
                        return true;
                    }
                    if ($valueContratoMoneda->ultimo_salto != null && $this->stringContains($contrato->ultimo_salto_m_y,
                        $input_lower)) {
                        return true;
                    }
                }
            }

            if (!Auth::user()->usuario_causante) {
                if ($this->stringContains($contrato->causante_nombre_color['nombre'], $input_lower)) {
                    return true;
                }
            }

            if ($no_redetermina xor $redetermina) {
                if ($no_redetermina && $contrato->no_redetermina) {
                    return true;
                }

                if ($redetermina && !$contrato->no_redetermina) {
                    return true;
                }
            }
            elseif ($no_redetermina && $redetermina) {
                return true;
            }

            return $this->stringContains($contrato->estado_creacion, $input_lower)
              || $this->stringContains($contrato->numero_contratacion, $input_lower)
              || $this->stringContains($contrato->denominacion, $input_lower)
              || $this->stringContains($contrato->numero_contrato, $input_lower)
              || $this->stringContains($contrato->contratista_nombre_documento, $input_lower)
              || $this->stringContains($contrato->resoluc_adjudic, $input_lower)
              || $this->stringContains($contrato->expediente_madre, $input_lower)
              || $this->stringContains($contrato->estado_nombre_color['nombre'], $input_lower)
              || $this->stringContains($contrato->resoluc_adjudic, $input_lower);
        });
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     */
    public function exportar(Request $request, $publicados)
    {
        $input = $request->all();
        $filtro = $input['excel_input'];

        $contratos_admin = Auth::user()->contratos_admin;

        if ($filtro != '') {
            $contratos_admin = $this->filtrar($contratos_admin, $this->minusculaSinAcentos($filtro));
        }

        $contratos = $contratos_admin->map(function ($contrato, $key) use ($publicados) {
            $arr = array();

            $arr[trans('contratos.numero_contrato')] = $contrato->numero_contrato;
            $arr[trans('forms.contratista')] = $contrato->contratista_nombre_documento;
            $arr[trans('contratos.numero_contratacion')] = $contrato->numero_contratacion;
            $arr[trans('forms.expediente_madre')] = $contrato->expediente_madre;
            $arr[trans('contratos.resoluc_adjudic')] = $contrato->resoluc_adjudic;
            $arr[trans('forms.denominacion')] = $contrato->denominacion;

            $montos = '';
            if ($contrato->tiene_contratos_monedas) {
                foreach ($contrato->contratos_monedas as $keyContratoMoneda => $valueContratoMoneda) {
                    if ($valueContratoMoneda->monto_vigente != null && $valueContratoMoneda->moneda != null) {
                        $montos .= $valueContratoMoneda->moneda->simbolo.' '.number_format(round($valueContratoMoneda->monto_vigente,
                            2), 0, ',', '.').' ';
                    }
                }
            }
            $arr[trans('forms.montos')] = $montos;

            $arr[trans('forms.ultimo_salto')] = $contrato->ultimo_salto_m_y;
            $arr[trans('forms.ultima_solicitud')] = $contrato->ultima_solicitud;

            if ($publicados == 'true' || $publicados == 1) {
                $vr = '';
                $separador_vr = ' - ';
                $vr = '';
                $separador_vr = ' - ';
                foreach ($contrato->contratos_monedas as $keyContratoMoneda => $valueContratoMoneda) {
                    if ($valueContratoMoneda->ultima_variacion != null) {
                        $vr .= ''.$valueContratoMoneda->ultima_variacion->variacion_show.$separador_vr;
                    }
                    else {
                        $vr .= '1'.$separador_vr;
                    }
                }
                if (substr($vr, -3) == $separador_vr) {
                    $vr = substr($vr, 0, -3);
                }

                $arr[trans('forms.vr')] = $vr;
            }

            if (!Auth::user()->usuario_causante) {
                if ($contrato->causante_id != null) {
                    $arr[trans('forms.causante')] = $contrato->causante->nombre;
                }
            }

            $estado = '';
            if ($contrato->estado_id != null) {
                $estado = $contrato->estado_nombre_color['nombre'];
            }

            $arr[trans('forms.estado')] = $estado;

            return $arr;
        });

        return $this->toExcel(trans('index.contratos'), $this->filtrarExportacion($contratos, $filtro));
    }

    //#endregion

    //#region  Salto

    /**
     * @param  int  $id_variacion
     * @param  int|nullable  $id_cuadro
     */
    public function verSalto($id_variacion, $id_cuadro = null)
    {
        $salto = VariacionMesPolinomica::findOrFail($id_variacion);

        if ($salto->empalme) {
            return redirect()->route('contratos.ver.incompleto', [
              'id' => $salto->contrato_moneda->contrato->id, 'accion' => 'polinomica'
            ]);
        }

        $calculador = new CalculoModelExtended;

        if ($id_cuadro != null) {
            $cuadro_comparativo = CuadroComparativo::find($id_cuadro);
        }
        else {
            $cuadro_comparativo = null;
        }

        return view('contratos.contratos.show.saltos.index', compact('salto', 'calculador', 'cuadro_comparativo'));
    }

    //#endregion

    //#region Creacion-Edicion de Contratos

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $causantes = Causante::getOpciones();
        $organos = OrganoAprobador::getOpciones();
        $contratistas = Contratista::getOpciones();

        $monedas = Moneda::getOpciones();
        $estados = EstadoContrato::getOpciones();
        $plazos = Plazo::all();
        $repres_tec_eby = User::whereUsuarioSistema(1)->get()->filter(function ($user) {
            return $user->can('realizar-inspeccion');
        })->sortBy('apellido')->pluck('apellido'.'nombre', 'id');

        $contrato = new Contrato();

        $tipo_contrato = TipoContrato::where('nombre', 'contrato')->first();

        return view('contratos.contratos.createEdit',
          compact('contrato', 'causantes', 'organos', 'contratistas', 'monedas', 'estados', 'repres_tec_eby', 'plazos',
            'tipo_contrato'));
    }

    /**
     * @param  int  $id
     */
    public function edit($id)
    {
        $contrato = Contrato::findOrFail($id);
        if (!$contrato->is_contrato) {
            return redirect()->route('contratos.index');
        }

        if (!Auth::user()->puedeVerCausante($contrato->causante_id)) {
            Log::error(trans('mensajes.error.no_pertenece_causante'), ['Usuario' => Auth::user()->id]);

            Session::flash('error', trans('mensajes.error.no_pertenece_causante'));
            return redirect()->route('contratos.index')->with(['error' => trans('mensajes.error.no_pertenece_causante')]);
        }

        if (!$contrato->guardadoDefinitivo && $contrato->incompleto['status']) {
            if (!$contrato->borrador && !$contrato->incompleto['status']) {
                Session::flash('error', trans('mensajes.error.contrato_borrador'));
                return redirect()->route('contratos.index')->with(['error' => trans('mensajes.error.contrato_borrador')]);
            }
        }

        $causantes = Causante::getOpciones();
        $organos = OrganoAprobador::getOpciones();
        $contratistas = Contratista::getOpciones();
        $monedas = Moneda::getOpciones();
        $estados = EstadoContrato::getOpciones();
        $plazos = Plazo::all();

        $repres_tec_eby = User::whereUsuarioSistema(1)->get()->filter(function ($user) {
            return $user->can('realizar-inspeccion');
        })->sortBy('apellido')->pluck('apellido'.'nombre', 'id');

        $repres_tec_eby_old = RepresentanteEby::where('contrato_id', $id)->pluck('user_id')->toArray();

        return view('contratos.contratos.createEdit',
          compact(
            'contrato',
            'causantes',
            'organos',
            'contratistas',
            'monedas',
            'estados',
            'repres_tec_eby',
            'repres_tec_eby_old',
            'plazos'
          ));
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     */
    public function storeUpdate(Request $request)
    {
        // Tdos los campos menos el token y el insptector de obra
        $input = $request->except(['_token', 'repre_tec_eby_id']);

        // Tipo de contrato
        $tipoContrato = TipoContrato::find($input['tipo_id']);

        // Es ceridicacion de adenda?
        $isAdendaCertificacion = $tipoContrato && $tipoContrato->nombre == 'adenda_certificacion';

        // Id de contrato
        $id = isset($input['id']) ? $input['id'] : null;

        // Contrato
        $contrato = $id ? Contrato::find($id) : null;

        // Contrato completo
        $contratoCompleto = $input['contrato_completo'];

        // Id pe padre
        $padreId = $input['padre_id'];

        // Estado
        $estadoId = $input['estado_id'];

        // Obtiene el causante del usuario
        if (Auth::user()->usuario_causante)
            $input['causante_id'] = Auth::user()->causante_id;

        // Doble firma
        $causante = null;

        if (!empty($input['causante_id']))
            $causante = Causante::find($input['causante_id']);

        if ($causante)
            $this->inputDoblefirma($input, $causante);

        // Edición definitiva
        $edicionDefinitivo = $contrato && !$contrato->borrador;

        // Es borrador
        $borrador = !$edicionDefinitivo ? $input['borrador'] : false;

        // Verificar permisos de edición de borrador
        if ($contrato && !$contrato->borrador && Auth::user()->cant('contrato-edit-borrador'))
            return $this->errorJsonResponse([trans('mensajes.error.contrato_borrador')]);

        // Verificar permisos de creación
        if (!$contrato && Auth::user()->cant('contrato-create'))
            return $this->errorJsonResponse([trans('mensajes.error.permisos')]);

        // Verificar permiso de edición
        if ($contrato && Auth::user()->cant('contrato-edit'))
            return $this->errorJsonResponse([trans('mensajes.error.permisos')]);

        //#region Validator de Representante Eby
        $definitivo = Arr::get($input, 'definitivo');
        $repre_eby = null;

        if ((!$contrato && !$contratoCompleto && !$borrador) // No existe y no es borrador
          || ($contrato && $contratoCompleto && !$borrador && !$definitivo) // Crear defi
          || ($contrato && !$contratoCompleto && !$borrador)  // Guardar borrador como defi
          || $borrador // Es borrador
        ) {
            $hay_repres = false;

            if ($request->has('repre_tec_eby_id')) {
                $repre_eby = $request->get('repre_tec_eby_id');

                foreach ($repre_eby as $repre) {
                    if ($repre != null) {
                        $hay_repres = true;
                        break;
                    }
                }
            }

            // Retorna error en el caso de que no haya representate y sea guardado definitivo
            if (!$hay_repres && !$borrador) {
                $errores['repre_tec_eby_id'] = trans('forms.multiple.repres_tec_eby');

                return $this->errorJsonResponse([trans('mensajes.error.revisar')], false, [
                  'errores' => $errores,
                ]);
            }
        }
        //#endregion

        // Estado adjudicado
        // TODO Cachar esta consulta
        $estado_adjudicada = EstadoContrato::whereNombre('adjudicada')
          ->first()
          ->id;

        //#region Edición definitiva
        if ($contratoCompleto) {

            // Verifica permisos de edición
            if (!$contrato->borrador && Auth::user()->cant('contrato-edit'))
                return $this->errorJsonResponse([trans('mensajes.error.contrato_borrador')]);

            //#region Validación de campos
            $rules = [
              'denominacion' => 'required|min:3|max:255', 'numero_contrato' => 'required|min:3|max:6',
              'estado_id' => 'required', 'expediente_madre' => 'required',
              'repre_leg_contratista' => 'required|min:3|max:1024', 'repre_tec_contratista' => 'required',
              'organo_aprobador_id' => 'required', 'resoluc_adjudic' => 'required|min:3|max:50'
            ];

            if (
              $estadoId != $estado_adjudicada &&
              $estadoId != $contrato->estado_id
              && $contrato->fecha_acta_inicio != null
            )
                $rules['fecha_acta_inicio'] = 'required|'.$this->dateFormat();

            $validator = Validator::make($input, $rules);
            $errores = [];

            if ($validator->fails() || sizeof($errores) > 0) {
                return $this->errorJsonResponse([trans('mensajes.error.revisar')], false, [
                  'errores' => array_merge($errores, $validator->getMessageBag()->toArray()),
                ]);
            }
            //#endregion

            // El estado no es Adjudicada o no a cambiado
            if ($estadoId != $estado_adjudicada || $estadoId == $contrato->estado_id) {

                // Si no es borrador y la fecha acta inicio esta definida
                if (
                  !$contrato->borrador
                  && !$contrato->fecha_acta_inicio
                  && !empty($input['fecha_acta_inicio'])
                ) {
                    // Actualizar los datos
                    $contrato->update($input);

                    if (!$contrato->doble_firma)
                        $this->publish($contrato);
                }
                else {

                    if (isset($input['monto_inicial'])) {
                        foreach ($input['monto_inicial'] as $keyContratoMoneda => $valueContratoMoneda) {
                            try {
                                $contratoMoneda = ContratoMoneda::whereId($keyContratoMoneda)->first();
                                $contratoMoneda->monto_inicial = $this->dosDecToDB($valueContratoMoneda);
                                $contratoMoneda->monto_vigente = $this->dosDecToDB($valueContratoMoneda);
                                $contratoMoneda->saldo = $this->dosDecToDB($valueContratoMoneda);
                                $contratoMoneda->save();
                            }
                            catch (QueryException $e) {
                                Log::error('QueryException', ['Exception' => $e]);

                                $jsonResponse['status'] = false;
                                $jsonResponse['title'] = trans('index.crear');
                                $jsonResponse['errores'] = $errores;
                                $jsonResponse['message'] = [trans('mensajes.error.insert_db')];

                                return response()->json($jsonResponse);
                            }
                        }
                    }

                    if ($repre_eby != null && count($repre_eby) > 0) {
                        $contrato->representante_eby->each->delete();

                        foreach ($repre_eby as $key => $eby) {
                            RepresentanteEby::create([
                              'user_id' => $eby, 'contrato_id' => $contrato->id,
                            ]);
                        }
                    }

                    // Actualizar los datos
                    $contrato->update($input);
                }

                $jsonResponse['status'] = true;
                $jsonResponse['refresh'] = true;

                $message = trans($contrato->is_contrato ? 'mensajes.dato.contrato' : 'mensajes.dato.adenda')
                  . trans('mensajes.success.editado');

                $jsonResponse['message'] = [
                  $message,
                ];

                Session::flash('success', $message);

                return response()->json($jsonResponse);
            }
        }
        //#endregion

        $input['no_redetermina'] = isset($input['no_redetermina']);
        $input['empalme'] = isset($input['empalme']);
        $input['requiere_garantia'] = isset($input['requiere_garantia']);

        if (!$edicionDefinitivo) {
            if ($input['fondo_reparo'] == "" || $input['fondo_reparo'] == null) {
                $input['fondo_reparo'] = null;
            }
            else {
                $input['fondo_reparo'] = str_replace([" ", "%", "."], ["", "", ""], $input['fondo_reparo']);
                $input['fondo_reparo'] = (float) str_replace(",", ".", $input['fondo_reparo']);
            }

            if ($input['anticipo'] == "" || $input['anticipo'] == null) {
                $input['anticipo'] = null;
            }
            else {
                $input['anticipo'] = str_replace([" ", "%", "."], ["", "", ""], $input['anticipo']);
                $input['anticipo'] = (float) str_replace(",", ".", $input['anticipo']);
            }
        }

        $required = $borrador ? 'nullable|' : 'required|';

        $rules = $this->getRulesContrato($required);

        if (isset($input['estado_id']) && $estadoId != $estado_adjudicada)
            $rules['fecha_acta_inicio'] = 'required|'.$this->dateFormat();

        // Validar reglas
        $validator = Validator::make($input, $rules);

        $errores = [];

        // Validaciones en el caso de que haya contrato padre
        if ($padreId == null) {
            if (!empty($input['numero_contrato'])) {
                $contrato_old = Contrato::whereNumeroContrato($input['numero_contrato'])
                  ->where('id', '<>', $id)
                  ->first();

                if ($contrato_old != null)
                    $errores['numero_contrato'] = trans('validation_custom.distinct.numero_contrato');
            }

            if (!empty($input['numero_contratacion'])) {
                $contrato_old = Contrato::whereNumeroContratacion($input['numero_contratacion'])
                  ->where('id', '<>', $id)
                  ->first();

                if ($contrato_old != null)
                    $errores['numero_contratacion'] = trans('validation_custom.distinct.numero_contratacion');
            }
        }

        if (!$edicionDefinitivo) {
            if ($input['anticipo'] != null) {
                if ($input['anticipo'] < 0 || $input['anticipo'] > 100) {
                    $errores['anticipo'] = trans('validation.between.numeric', [
                      'attribute' => trans('contratos.anticipo'), 'min' => 0, 'max' => 100
                    ]);
                }
                else if (strlen(substr(strrchr($input['anticipo'], "."), 1)) > 2)
                    $errores['anticipo'] = trans('mensajes.error.max_decimal_2');
            }

            if ($input['fondo_reparo'] != null) {
                if ($input['fondo_reparo'] < 0 || $input['fondo_reparo'] > 100) {
                    $errores['fondo_reparo'] = trans('validation.between.numeric', [
                      'attribute' => trans('contratos.fondo_reparo'), 'min' => 0, 'max' => 100
                    ]);
                }
                else if (strlen(substr(strrchr($input['fondo_reparo'], "."), 1)) > 2)
                    $errores['fondo_reparo'] = trans('mensajes.error.max_decimal_2');
            }

            if (
              !$borrador
              && !empty($input['fecha_acta_inicio'])
              && !(
                DateTime::createFromFormat('d/m/Y', $input['fecha_oferta'])
                <= DateTime::createFromFormat('d/m/Y', $input['fecha_acta_inicio'])
              )
            ) {
                if (!$padreId)
                    $errores['fecha_acta_inicio'] = trans('validation_custom.fecha.inicio_anterior_oferta');
                else
                    $errores['fecha_acta_inicio'] = trans('validation_custom.fecha.inicio_anterior_oferta_contrato');
            }
        }

        //#region Armar ContratoMoneda
        $ids_monedas = [];

        if (!empty($input['moneda_id'])) {
            foreach ($input['moneda_id'] as $keyMoneda => $valueMoneda) {
                if (!empty($valueMoneda)) {
                    $hay_moneda = true;
                    $ids_monedas[$keyMoneda] = $keyMoneda;
                }
            }
        }


        $arr_contrato_moneda = [];
        $cant_monedas = [];

        if (empty($contrato)) {
            if ($id != null) {
                $contrato = Contrato::find($id);
            }
            else {
                $contrato = new Contrato();
            }
        }

        foreach ($ids_monedas as $keyIdMoneda => $valueIdMoneda) {
            $id_moneda = $input['moneda_id'][$valueIdMoneda];

            if (!isset($cant_monedas[$id_moneda])) {
                $cant_monedas[$id_moneda] = 0;
            }
            else {
                $cant_monedas[$id_moneda]++;
            }

            if (!isset($input['tasa_cambio'][$valueIdMoneda])) {
                $input['tasa_cambio'][$valueIdMoneda] = null;
            }

            $arr_contrato_moneda[$keyIdMoneda] = [
              'clase_type' => $contrato->getClassName(), 'clase_id' => $id, 'moneda_id' => $id_moneda,
              'monto_inicial' => ($input['monto_inicial'][$valueIdMoneda] == null) ? null : $this->dosDecToDB($input['monto_inicial'][$valueIdMoneda]),
              'monto_vigente' => ($input['monto_inicial'][$valueIdMoneda] == null) ? null : $this->dosDecToDB($input['monto_inicial'][$valueIdMoneda]),
              'tasa_cambio' => ($input['tasa_cambio'][$valueIdMoneda] == null) ? null : $this->dosDecToDB($input['tasa_cambio'][$valueIdMoneda]),
              'monto_vigente_val_originales' => ($input['monto_inicial'][$valueIdMoneda] == null) ? null : $this->dosDecToDB($input['monto_inicial'][$valueIdMoneda]),
              'tasa_cambio' => ($input['tasa_cambio'][$valueIdMoneda] == null) ? null : $this->dosDecToDB($input['tasa_cambio'][$valueIdMoneda]),
              'fecha_ultima_redeterminacion' => null, 'mes_ultima_certificacion' => null, 'saldo' => null,
            ];
        }

        foreach ($cant_monedas as $keyCant => $valueCant) {
            if ($valueCant > 0) {
                $errores['monedas_duplicadas'] = trans('validation_custom.moneda_duplicada', [
                  'moneda' => Moneda::find($keyCant)->nombre_simbolo
                ]);
            }
        }

        if ($isAdendaCertificacion) {
            $rulesContratoMoneda = [
              'monto_inicial' => $required,
            ];
        }
        else {
            $rulesContratoMoneda = [
              'monto_inicial' => $required,
              'tasa_cambio' => $required,
            ];
        }

        foreach ($arr_contrato_moneda as $keyArr => $valueArr) {
            $validatorContratoMoneda = Validator::make($valueArr, $rulesContratoMoneda);

            $error = $this->validarTamanio($valueArr, 'monto_inicial', $keyArr);

            if (sizeof($error) > 0) {
                $errores = array_merge($errores, $error);
            }

            $error = $this->validarTamanio($valueArr, 'tasa_cambio', $keyArr);

            if (sizeof($error) > 0) {
                $errores = array_merge($errores, $error);
            }

            $error = $this->validarTamanio($valueArr, 'tasa_cambio', $keyArr);

            if (sizeof($error) > 0) {
                $errores = array_merge($errores, $error);
            }

            if (
              !$borrador
              && isset($valueArr['mes_ultima_certificacion'])
              && $valueArr['mes_ultima_certificacion'] != null
              && isset($input['fecha_acta_inicio'])
              && $input['fecha_acta_inicio'] != null
              && isset($input['plazo']) && $input['plazo'] != null
              && isset($input['plazo_id']) && $input['plazo_id'] != null
            ) {
                $es_primero = (int) explode('/', $this->fecha_acta_inicio)[0] == 1;

                if (Plazo::find($input['plazo_id'])->nombre == 'Dias') {
                    $meses = (int) ($input['plazo'] / 30);
                }
                else {
                    $meses = $input['plazo'];
                }

                if (!$es_primero) {
                    $meses = $meses + 1;
                }

                if ($valueArr['mes_ultima_certificacion'] > $meses) {
                    $errores['mes_ultima_certificacion_'.$keyArr] = trans('validation_custom.mes_ultima_certificacion_mayor_meses');
                }
            }

            if ($validatorContratoMoneda->fails() || sizeof($errores) > 0) {
                $erroresMonedas = $validatorContratoMoneda->getMessageBag()->toArray();

                foreach ($erroresMonedas as $keyErrorMoneda => $valueErrorMoneda) {
                    foreach ($valueErrorMoneda as $keyError => $valueError) {
                        if (!isset($errores[$keyErrorMoneda.'_'.$keyArr])) {
                            $errores[$keyErrorMoneda.'_'.$keyArr] = $valueErrorMoneda;
                        }
                        else {
                            $errores[$keyErrorMoneda.'_'.$keyArr] .= '<br>'.$valueErrorMoneda;
                        }
                    }
                }
            }
        }
        //#endregion

        if (!$borrador && !isset($hay_moneda)) {
            $errores['monedas'] = trans('validation_custom.sin_monedas');
        }

        if ($validator->fails() || sizeof($errores) > 0) {
            return $this->errorJsonResponse([trans('mensajes.error.revisar')], false, [
              'errores' => array_merge($errores, $validator->getMessageBag()->toArray())
            ]);
        }
        else {

            if (!$contrato) {
                $contrato = new Contrato();
            }

            $contrato->fill($input);

            if ($padreId != null) {
                $contrato->contrato_padre_id = $padreId;
            }

            $contrato->tipo_id = $input['tipo_id'];
            $contrato->save();
            $id = $contrato->id;

            if ($repre_eby != null && count($contrato->representante_eby)) {
                $contrato->representante_eby->each->delete();
            }

            if ($repre_eby != null && count($repre_eby) > 0) {
                foreach ($repre_eby as $key => $eby) {
                    RepresentanteEby::create([
                      'user_id' => $eby, 'contrato_id' => $contrato->id,
                    ]);
                }
            }

            if (isset($input['adjunto']) && $request->hasFile('adjunto')) {
                $contrato->adjuntos->each->delete();

                foreach ($input['adjunto'] as $keyAdjunto => $valueAdjunto) {
                    $adjuntos_json = $this->uploadFile($request, $contrato->id, 'adjunto|'.$keyAdjunto, 'contrato');

                    ContratoAdjunto::create([
                      'clase_id' => $id, 'clase_type' => $contrato->getClassName(), 'adjunto' => $adjuntos_json,
                    ]);
                }
            }

            $contratoMonedas = collect();
            $monedas_existentes = [];

            //#region Creacion de ContratoMoneda
            foreach ($arr_contrato_moneda as $keyArrContratoMoneda => $valueArrContratoMoneda) {
                $valueArrContratoMoneda['clase_id'] = $id;
                $valueArrContratoMoneda['clase_type'] = $contrato->getClassName();
                $contratoMoneda = ContratoMoneda::whereClaseId($id)->whereClaseType($contrato->getClassName())->whereMonedaId($valueArrContratoMoneda['moneda_id'])->first();
                $monedas_existentes[$valueArrContratoMoneda['moneda_id']] = $valueArrContratoMoneda['moneda_id'];
                $valueArrContratoMoneda['saldo'] = $valueArrContratoMoneda['monto_inicial'];

                if (!$contratoMoneda)
                    $contratoMoneda = new ContratoMoneda($valueArrContratoMoneda);
                else
                    $contratoMoneda->update($valueArrContratoMoneda);

                $contratoMoneda->save();
                $contratoMonedas->push($contratoMoneda);
            }
            //#endregion

            ContratoMoneda::whereClaseId($id)
              ->whereClaseType($contrato->getClassName())->whereNotIn('moneda_id', $monedas_existentes)
              ->delete();

            if ($borrador) {
                $jsonResponse['status'] = true;

                if ($contrato) {
                    if ($contrato->is_contrato) {
                        $jsonResponse['url'] = route('contratos.index');
                        $message = trans('mensajes.dato.contrato').trans('mensajes.success.creado');
                    }
                    else {
                        $jsonResponse['url'] = route('contratos.ver', ['id' => $padreId]);
                        $message = trans('mensajes.dato.adenda').trans('mensajes.success.creada');
                    }
                }
                else {
                    $jsonResponse['refresh'] = true;

                    if ($contrato->is_contrato) {
                        $message = trans('mensajes.dato.contrato').trans('mensajes.success.editado');
                    }
                    else {
                        $message = trans('mensajes.dato.adenda').trans('mensajes.success.editada');
                    }
                }

                Session::flash('success', $message);
                $jsonResponse['message'] = [$message];

                $jsonResponse['id'] = $id;

                return response()->json($jsonResponse);
            }
            else {

                $is_adenda_ampliacion = (bool) $contrato->is_adenda_ampliacion;

                foreach ($contratoMonedas as $valueContratoMoneda) {
                    $itemizado = Itemizado::create([]);
                    $valueContratoMoneda->itemizado_id = $itemizado->id;

                    if ($is_adenda_ampliacion) {
                        $contrato_moneda_madre = ContratoMoneda::whereClaseId($contrato->contrato_padre_id)
                          ->whereClaseType($contrato->contrato_padre->getClassName())
                          ->whereMonedaId($valueContratoMoneda->moneda_id)->first();

                        $itemizadoMadre = $contrato_moneda_madre->itemizado;

                        if (count($itemizadoMadre->items) > 0) {
                            DB::transaction(function () use ($itemizadoMadre, $itemizado, $meses) {
                                $this->duplicateItems($itemizadoMadre->items_nivel_1, $itemizado->id, null, $meses,
                                  false, null);
                            });
                        }
                    }

                    if ($input['no_redetermina'] == 0) {
                        $polinomica = Polinomica::create([]);

                        $valueContratoMoneda->polinomica_id = $polinomica->id;
                    }

                    $valueContratoMoneda->save();
                }

                $this->createInstanciaHistorial($contrato, 'itemizado', 'borrador');

                $this->createInstanciaHistorial($contrato, 'polinomica', 'borrador');

                $jsonResponse['status'] = true;

                if ($contrato->is_contrato) {
                    $message = trans('mensajes.dato.contrato').trans('mensajes.success.guardado');
                    $jsonResponse['url'] = route('contratos.index');
                }
                else {
                    $message = trans('mensajes.dato.adenda').trans('mensajes.success.creada');
                    $jsonResponse['url'] = route('contratos.ver', ['id' => $padreId]);
                }

                Session::flash('success', $message);
                $jsonResponse['message'] = [$message];
                $jsonResponse['id'] = $id;

                return response()->json($jsonResponse);
            }
        }
    }

    public function sign($id) {
        // Contrato
        $contrato = Contrato::find($id);

        // Tipo de contrato
        $tipoContrato = TipoContrato::find($contrato->tipo_id);

        // Es ceridicacion de adenda?
        $isContrato = $tipoContrato && $tipoContrato->nombre == 'contrato';

        // Relaciones
        $contrato->load('causante');

        // Causante
        $causante = $contrato->causante;

        if (!$causante) {
            return $this->errorJsonResponse([trans('mensajes.error.doble_firma_sin_causante', [
              'name' => trans('index.contrato')
            ])]);
        }

        // Verifica que si adminte doble firma
        if (!$causante->doble_firma) {
            return $this->errorJsonResponse([trans('mensajes.error.doble_firma_no_admite', [
              'name' => trans('index.contrato')
            ])]);
        }

        // Verifica se si es uno de los jefes que deben firmar
        $firma_ar = $causante->jefe_contrato_ar;
        $firma_py = $causante->jefe_contrato_py;

        if (!in_array(Auth::user()->id, [$firma_ar, $firma_py])) {
            return $this->errorJsonResponse([trans('mensajes.error.doble_firma_no_es_jefe', [
              'name' => trans('index.contrato')
            ])]);
        }

        // Firma si es el del lado argentino
        if ($firma_ar == Auth::user()->id)
            $contrato->firma_ar =  Auth::user()->id;

        // Firma si es el del lado paraguay
        if ($firma_py == Auth::user()->id)
            $contrato->firma_py =  Auth::user()->id;

        // Firma conseguida
        if($contrato->firma_ar && $contrato->firma_py)
            $contrato->doble_firma = false;

        // Guardar cambios
        $contrato->save();

        // Si ya estan las 2 firmas genera los elementos necesarios para completar el contrato
        if ($isContrato && $contrato->firma_ar && $contrato->firma_py)
            $this->publish($contrato);

        // Respuesta
        $response = [];
        $response['status'] = true;
        $response['refresh'] = true;

        $message = trans('mensajes.success.firmado', [
          'type' => $isContrato ? 'contrato' : 'adenda',
          'name' => $contrato->denominacion
        ]);

        $response['message'] = [
          $message,
        ];

        Session::flash('success', $message);

        return response()->json($response);
    }

    public function draft($id) {
        // Contrato
        $contrato = Contrato::find($id);

        // Relaciones
        $contrato->load('causante');

        // Causante
        $causante = Causante::find($contrato->causante_id);

        if (!$causante) {
            return $this->errorJsonResponse([ trans('mensajes.error.doble_firma_sin_causante', [
              'name' => trans('index.contrato')
            ]) ]);
        }

        // Verifica que si adminte doble firma
        if (!$causante->doble_firma) {
            return $this->errorJsonResponse([trans('mensajes.error.doble_firma_no_admite', [
              'name' => trans('index.contrato')
            ])]);
        }

        // Verifica se si es uno de los jefes que deben firmar
        $firma_ar = $causante->jefe_contrato_ar;
        $firma_py = $causante->jefe_contrato_py;

        if (!in_array(Auth::user()->id, [$firma_ar, $firma_py])) {
            return $this->errorJsonResponse([trans('mensajes.error.doble_firma_no_es_jefe', [
              'name' => trans('index.contrato')
            ])]);
        }

        $contrato->firma_ar =  null;
        $contrato->firma_py =  null;
        $contrato->doble_firma = false;
        $contrato->borrador = true;

        // Guardar cambios
        $contrato->save();

        $response = [];
        $response['status'] = true;
        $response['refresh'] = true;

        $message = trans('mensajes.success.borrador', [
            'type' => trans('index.contrato'),
            'name' => $contrato->denominacion,
          ]);

        $response['message'] = [
          $message,
        ];

        Session::flash('success', $message);

        return response()->json($response);
    }

    //#endregion

    //#region Eliminar Contrato

    /**
     * @param  int  $id
     */
    public function preDelete($id)
    {
        $contrato = Contrato::find($id);

        if ((Auth::user()->cant($contrato->tipo_contrato->nombre.'-delete'))) {
            $jsonResponse['status'] = false;

            if ($contrato->is_contrato) {
                $jsonResponse['title'] = trans('index.eliminar').' '.trans('index.contrato');
            }
            else {
                $jsonResponse['title'] = trans('index.eliminar').' '.trans('forms.adenda');
            }

            $jsonResponse['message'] = [trans('mensajes.error.permisos')];
            return response()->json($jsonResponse);
        }

        if (!$contrato->borrador) {
            $jsonResponse['status'] = false;

            if ($contrato->is_contrato) {
                $jsonResponse['title'] = trans('index.eliminar').' '.trans('index.contrato');
                $jsonResponse['message'] = [trans('index.no_puede_eliminar.contrato')];
            }
            else {
                $jsonResponse['title'] = trans('index.eliminar').' '.trans('forms.adenda');
                $jsonResponse['message'] = [trans('index.no_puede_eliminar.adenda')];
            }

            return response()->json($jsonResponse);
        }
        else {
            $jsonResponse['status'] = true;
            return response()->json($jsonResponse);
        }
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

        $contrato = Contrato::find($id);
        $is_adenda = $contrato->is_adenda;

        try {
            if ($contrato->tiene_contratos_monedas) {
                foreach ($contrato->contratos_monedas as $valueContratoMoneda)
                    $valueContratoMoneda->delete();
            }

            $contrato->delete();

        }
        catch (QueryException $e) {
            Log::error('QueryException', ['Exception' => $e]);
            $jsonResponse['status'] = false;
            $jsonResponse['message'] = [trans('mensajes.error.insert_db')];
            return response()->json($jsonResponse);
        }

        $jsonResponse['status'] = true;
        $jsonResponse['refresh'] = true;

        if ($is_adenda) {
            Session::flash('success', trans('mensajes.dato.adenda').trans('mensajes.success.eliminado'));
            $jsonResponse['message'] = [trans('mensajes.dato.adenda').trans('mensajes.success.eliminado')];
        }
        else {
            Session::flash('success', trans('mensajes.dato.contrato').trans('mensajes.success.eliminado'));
            $jsonResponse['message'] = [trans('mensajes.dato.contrato').trans('mensajes.success.eliminado')];
        }

        return response()->json($jsonResponse);
    }

    //#endregion

    //#region Vistas ajax por Performance

    /**
     * @param  int  $id
     * @param  string  $seccion
     * @param  string  $version  |nullable
     * @param  string  $visualizacion  | nullable
     */
    public function getViews($id, $seccion, $version = 'vigente', $visualizacion = 'porcentaje')
    {
        if ($seccion == 'polinomica' && Auth::user()->cant('polinomica-edit')) {
            return redirect()
              ->route('contratos.index')
              ->with(['error' => trans('mensajes.error.contrato_borrador')]);
        }

        if ($seccion == 'itemizado' && Auth::user()->cant('itemizado-manage')) {
            return redirect()
              ->route('contratos.index')
              ->with(['error' => trans('mensajes.error.contrato_borrador')]);
        }

        if ($seccion == 'cronograma' && Auth::user()->cant('cronograma-manage')) {
            return redirect()
              ->route('contratos.index')
              ->with(['error' => trans('mensajes.error.contrato_borrador')]);
        }

        $contrato = Contrato::findOrFail($id);

        if (!Auth::user()->puedeVerCausante($contrato->causante_id)) {
            return redirect()->route('contratos.index');
        }

        $contrato->load('causante');

        // Monedas disponibles
        $monedas = new Collection();
        $contratoIncompleto = $contrato->incompleto;
        $unidadesMedida = [];
        $responsables = [];

        if ($contratoIncompleto['status']) {
            if (
              ($contratoIncompleto['polinomica'] && Auth::user()->can('polinomica-edit'))
              || ($contratoIncompleto['itemizado'] && Auth::user()->can('itemizado-edit'))
            ) {

                // Obtiene las monedas del contrato
                $monedas = ContratoMoneda::with('moneda')
                  ->whereClaseId($id)
                  ->whereClaseType($contrato->getClassName())
                  ->get();

                $ids = $monedas->pluck('moneda_id', 'moneda_id')->toArray();

                $fecha_oferta = $contrato->is_contrato
                  ?  $contrato->fecha_oferta
                  : $contrato->contrato_original->fecha_oferta;

                $indices = IndiceTabla1::whereIn('moneda_id', $ids)
                  ->where('fecha_inicio', '!=', null)
                  ->where('fecha_inicio', '<=', $this->fechaDeA($fecha_oferta, 'd/m/Y', 'Y-m-d'))
                  ->get()
                  ->groupBy('moneda_id');

                if($seccion == 'polinomica'){  
                    //Filtra los indices que estan discontinuados, los que en algun momento del contrato tienen valor 0  
                    $indices_a_borrar = ValorIndicePublicado::where('valor','=',"0.00")->get()->unique("tabla_indices_id");
                    foreach ($indices as $key => $indice_x_moneda) {
                        $indices[$key] = $indices[$key]->filter(function ($indice) use ($indices_a_borrar){    
                            return ! $indices_a_borrar->contains('tabla_indices_id',$indice->id);
                        });
                    }
                }

                if ($contratoIncompleto['itemizado'] && Auth::user()->can('itemizado-edit')) {
                    $unidadesMedida = UnidadMedida::getOpciones();
                    $responsables = Contratista::getOpciones();
                }
            }
        }

        $jsonResponse['highcharts'] = false;

        if ($visualizacion == 'curva_inversion') {
            $jsonResponse['highcharts'] = $visualizacion;
        }

        if (!($contrato->completo || !isset($contrato->incompleto[$seccion]) || $seccion == 'anticipos' || (!$contrato->incompleto[$seccion]))) {
            $visualizacion = 'all';
        }

        $opciones['version'] = $version;
        $opciones['visualizacion'] = $visualizacion;
        $fromAjax = true;
        $publicados = true;

        $data = compact(
          'contrato',
          'contratoIncompleto',
          'indices',
          'opciones',
          'fromAjax',
          'unidadesMedida',
          'publicados',
          'responsables',
          'monedas'
        );

        $jsonResponse['view'] = View::make("contratos.contratos.show.{$seccion}.index", $data)->render();

        $metodo = 'has_'.$seccion.'_vigente';

        if ($opciones['version'] == 'vigente' && $contrato->$metodo) {
            $id = $contrato->id;
        }

        // $id = $contrato->adenda_vigente_id;

        $jsonResponse['historial'] = route('contrato.historial', [
          'clase_id' => $id, 'clase_type' => $contrato->getClassNameAsKey(), 'seccion' => $seccion
        ]);

        return response()->json($jsonResponse);
    }

    //#endregion

    /**
     * @param  int  $clase_id
     * @param  string  $clase_type
     * @param  string  $seccion
     */
    public function historial($clase_id, $clase_type, $seccion)
    {
        $instancias = InstanciaContrato::whereClaseId($clase_id)->whereSeccion($seccion)->get()->filter(function (
          $instancia
        ) use ($clase_type) {
            return $this->toKey($instancia->clase_type) == $clase_type;
        });

        $jsonResponse['view'] = View::make('contratos.contratos.historial', compact('instancias', 'seccion'))->render();
        $jsonResponse['title'] = trans('index.de').' '.trans('contratos.'.$seccion);

        return response()->json($jsonResponse);
    }

    /**
     * Crea los elementos adicionales
     * @param $contrato
     */
    public function publish($contrato) {
        // Por defecto marca que no existe el cronograma
        $cronograma_existente = false;

        // Recorre el itemizdo
        foreach ($contrato->contratos_monedas as $valueContratoMoneda) {
            $itemizado = $valueContratoMoneda->itemizado;

            // Si el itemizado no es borrador
            if (!$itemizado->borrador) {
                // Crea el cronograma
                $this->createCronograma($itemizado);

                // Marca que existe el cronograma
                $cronograma_existente = true;
            }
        }

        // Si existe cronograma
        if (!$cronograma_existente) {
            // Crea una instacia de historial
            $this->createInstanciaHistorial($contrato, 'itemizado', 'aprobado');
        }

        // Completar contrato
        $this->completarContrato($contrato->id);

        $contrato->save();
    }

}
