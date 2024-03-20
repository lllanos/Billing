<?php

namespace App\Http\Controllers\Contratos;

use App\User;
use Contratista\Contratista;
use Contrato\Contrato;
use Contrato\ContratoMoneda\ContratoMoneda;
use Contrato\EstadoContrato;
use Contrato\OrganoAprobador;
use Contrato\Plazo;
use Contrato\RepresentanteEby;
use Contrato\TipoContrato;
use DateTime;
use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Itemizado\Itemizado;
use Log;
use View;
use Yacyreta\Causante;
use Yacyreta\Moneda;

class AdendasController extends ContratosControllerExtended
{

    public function __construct()
    {
        View::share('ayuda', 'contrato');
        $this->middleware('auth', ['except' => 'logout']);
    }

    #region Creacion de Adenda

    /**
     * @param  int  $contrato_id
     */
    public function create($contrato_id)
    {
        $contrato = Contrato::find($contrato_id);

        if (!Auth::user()->puedeVerCausante($contrato->causante_id)) {
            Log::error(trans('mensajes.error.no_pertenece_causante'), ['Usuario' => Auth::user()->id]);

            Session::flash('error', trans('mensajes.error.no_pertenece_causante'));
            return redirect()->route('contratos.index')
                ->with(['error' => trans('mensajes.error.no_pertenece_causante')]);
        }

        foreach (TipoContrato::where('nombre', '!=', 'contrato')->get() as $keyTipo => $valueTipo) {
            $nombre = $valueTipo->nombre;
            $metodo = 'permite_'.$nombre;
            if ($contrato->$metodo) {
                $select_options[$nombre] = $nombre;
            }
        }

        if (!isset($select_options)) {
            Session::flash('error', trans('adendas.sin_permisos'));
            return redirect()->route('contratos.index')
                ->with(['error' => trans('adendas.sin_permisos')]);
        }

        $adenda = new Contrato();
        $adenda->contrato_padre_id = $contrato_id;

        return view('contratos.adendas.createEdit', compact('select_options', 'contrato_id', 'adenda'));
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     */
    public function storeUpdate(Request $request)
    {
        $input = $request->except(['_token']);

        $contrato_padre_id = $input['contrato_padre_id'];
        $contrato_padre = Contrato::find($contrato_padre_id);

        if (!Auth::user()->puedeVerCausante($contrato_padre->causante_id))
            return $this->errorJsonResponse([trans('adendas.no_pertenece_causante')], route('contratos.index'));

        if (!$contrato_padre->permite_adendas)
            return $this->errorJsonResponse([trans('adendas.no_se_puede_crear')], route('contratos.index'));

        $tipo_adenda = TipoContrato::find($input['tipo_id'])->nombre;

        if (Auth::user()->cant($tipo_adenda . '-create'))
            return $this->errorJsonResponse([trans('adendas.sin_permisos')], route('contratos.index'));

        $jsonResponse = $this->$tipo_adenda($input);
        $jsonResponse = $jsonResponse->getData();

        return response()->json($jsonResponse);
    }

    /**
     * @param  array  $input
     */
    public function adenda_ampliacion($input)
    {
        // Validaciones Custom
        $borrador = $input['borrador'];

        $required = $borrador ? 'nullable|' : 'required|';

        $rules = $this->getRulesContrato($required);

        // Elimina reglas que no se apliacan a adenda
        unset($rules['numero_contratacion']);
        unset($rules['numero_contrato']);
        unset($rules['contratista_id']);
        unset($rules['organo_aprobador_id']);
        unset($rules['repre_leg_contratista']);
        unset($rules['causante_id']);
        unset($rules['fecha_oferta']);
        unset($rules['estado_id']);

        $rules['fecha_acta_inicio'] = $required . $this->dateFormat();
        $rules['fecha_aprobacion'] = $required . $this->dateFormat();

        $contrato = $input['id'] ? Contrato::find($input['id']) : null;

        $definitivo = false;

        if ($contrato) {
            $definitivo = !$contrato->borrador;

            unset($rules['plazo']);
            unset($rules['plazo_id']);
        }

        $errores = [];

        $contratoPadre = Contrato::find($input['contrato_padre_id']);
        $fecha_acta_inicio = !empty($input['fecha_acta_inicio'])
            ? DateTime::createFromFormat('d/m/Y', $input['fecha_acta_inicio'])
            : null;
        $fecha_aprobacion = !empty($input['fecha_aprobacion'])
            ? DateTime::createFromFormat('d/m/Y', $input['fecha_aprobacion'])
            : null;

        if ($definitivo && $fecha_acta_inicio && $fecha_aprobacion && $fecha_acta_inicio > $fecha_aprobacion)
            $errores['fecha_aprobacion'] = trans('validation_custom.fecha.aprobacion_anterior_inicio');

        if (!$definitivo) {
            foreach ($input['monto_inicial'] as $contratoMonedaId => $monto) {

                $certificados_basicos = $contratoPadre->certificados()
                    ->whereRedeterminado(0)
                    ->whereBorrador(0)
                    ->whereHas('cert_moneda_contratista', function ($query) use ($contratoMonedaId) {
                        $query->whereContratoMonedaId($contratoMonedaId);
                    })
                    ->get();

                $montoBasico = 0;

                foreach ($certificados_basicos as $valueCert) {
                    foreach ($valueCert->cert_moneda_contratista as $valueCertMoneda)
                        $montoBasico += $valueCertMoneda->monto_bruto;
                }

                // Monto inicial requeido
                if ($monto == ''|| $monto == null) {
                    $errores['monto_inicial_' . $contratoMonedaId] = trans('validation.required', [
                        'attribute' => trans('contratos.monto_inicial')
                    ]);

                    continue;
                }

                // Convierte el moto a float
                $monto = str_replace('.', '', $monto);
                $input['monto_inicial'][$contratoMonedaId] = $monto;
                $montoInicial = (float) str_replace(',', '.', $monto);

                // El monto inical deve ser major que el monto basico
                if ($montoInicial < $montoBasico)
                    $errores['monto_inicial_' . $contratoMonedaId] = trans('adendas.monto_ampliado_mayor');

                $error = $this->validarTamanio($input['monto_inicial'], $contratoMonedaId);

                $input['monto_inicial'][$contratoMonedaId] = $montoInicial;

                if (sizeof($error) > 0)
                    $errores = array_merge($errores, $error);
            }

            if (!$borrador && !$input['plazo'] && !$input['plazo_id']) {
                $plazo = Plazo::find($input['plazo_id'])->nombre;

                if ($plazo == 'Dias')
                    $meses = ceil($input['plazo'] / 30);
                else
                    $meses = $input['plazo'];

                $es_primero = $contratoPadre->fecha_acta_inicio->day == 1;

                if (!$es_primero)
                    $meses = $meses + 1;

                if ($contratoPadre->mes_proximo_certificado > $meses)
                    $errores['plazo'] = trans('validation_custom.meses_mayor_mes_ultima_certificacion');
            }
        }

        $validator = Validator::make($input, $rules);

        if ($validator->fails() || sizeof($errores) > 0) {
            $options['errores'] = array_merge($errores, $validator->getMessageBag()->toArray());
            return $this->errorJsonResponse([trans('mensajes.error.revisar')], null, $options);
        }

        $causante = null;

        if (!empty($contratoPadre->causante_id))
            $causante = Causante::find($contratoPadre->causante_id);

        if ($causante)
            $this->inputDoblefirma($input, $causante);

        if ($contrato)
            $contrato->update($input);
        else {
            $contrato = new Contrato($input);
            $contrato->contratista_id = $contratoPadre->contratista_id;
            $contrato->causante_id = $contratoPadre->causante_id;
            $contrato->save();
        }

        if (!$definitivo) {
            foreach ($input['monto_inicial'] as $keyContratoMoneda => $valueContratoMoneda) {
                $moneda_id = ContratoMoneda::find($keyContratoMoneda)->moneda_id;

                $contratoMoneda = ContratoMoneda::whereClaseId($contrato->id)
                    ->whereClaseType($contrato->getClassName())
                    ->whereMonedaId($moneda_id)
                    ->first();

                if (!$contratoMoneda) {
                    $contratoMoneda = new ContratoMoneda();
                    $contratoMoneda->moneda_id = $moneda_id;
                    $contratoMoneda->clase_id = $contrato->id;
                    $contratoMoneda->clase_type = $contrato->getClassName();
                    $contratoMoneda->monto_inicial = $valueContratoMoneda;
                    $contratoMoneda->monto_vigente = $valueContratoMoneda;
                }
                else {
                    $contratoMoneda->monto_inicial = $valueContratoMoneda;
                    $contratoMoneda->monto_vigente = $valueContratoMoneda;
                }

                $contratoMoneda->save();
            }
        }

        if (!$definitivo && !$borrador) {

            $contrato->load(['contratos_monedas.itemizado']);

            $montosActuales = $contratoPadre->montosVigentesActuales($contrato->id);
            $contratoMonedas = $contrato->contratos_monedas;

            foreach ($contratoMonedas as $contratoMoneda) {

                if ($contratoMoneda->itemizado)
                    continue;

                $itemizado = Itemizado::create([]);
                $contratoMoneda->itemizado_id = $itemizado->id;
                $contratoMoneda->save();

                $montoVigenteOriginal = $montosActuales[$contratoMoneda->moneda_id];
                $contratoMoneda->monto_ampliado = $contratoMoneda->monto_inicial - $montoVigenteOriginal;
                $contratoMoneda->save();

                if ($contrato->is_adenda) {
                    $padreMoneda = ContratoMoneda::whereClaseId($contrato->contrato_padre_id)
                        ->whereClaseType($contrato->getClassName())
                        ->whereMonedaId($contratoMoneda->moneda_id)
                        ->first();

                    $padreItemizado = $padreMoneda->itemizado_actual;

                    $itemizado->total = $padreItemizado->total;
                    $itemizado->save();

                    $createCronograma = $this->createCronograma($itemizado);
                    $cronograma_id = $createCronograma['cronograma_id'];
                    $meses = $createCronograma['meses'];

                    if (count($padreItemizado->items) > 0) {
                        DB::transaction(function () use ($padreItemizado, $itemizado, $cronograma_id, $meses) {
                            $this->duplicateItems($padreItemizado->items_nivel_1, $itemizado->id, $cronograma_id, $meses, false, null);
                        });
                    }
                }
            }

            $this->createInstanciaHistorial($contrato, 'itemizado', 'borrador');
        }

        if ($input['id'] == null) {
            Session::flash('success', trans('mensajes.dato.adenda').trans('mensajes.success.creada'));
            $jsonResponse['message'] = [trans('mensajes.dato.adenda').trans('mensajes.success.creada')];
        }
        else {
            Session::flash('success', trans('mensajes.dato.adenda').trans('mensajes.success.editada'));
            $jsonResponse['message'] = [trans('mensajes.dato.adenda').trans('mensajes.success.editada')];
        }

        $jsonResponse['status'] = true;
        $jsonResponse['url'] = route('contratos.ver.incompleto', [
            'id' => $input['contrato_padre_id'],
            'adenda_id' => $contrato->id,
            'accion' => 'adendaAmpliacion']
        );

        return response()->json($jsonResponse);
    }

    #endregion

    #region Edición de Adendas

    /**
     * @param  int  $adenda_id
     */
    public function edit($adenda_id)
    {
        $adenda = Contrato::findOrFail($adenda_id);

        if (!$adenda->is_adenda)
            return redirect()->route('contratos.index');

        $contrato_padre = $adenda->contrato_padre;

        if (!Auth::user()->puedeVerCausante($contrato_padre->causante_id)) {
            Log::error(trans('mensajes.error.no_pertenece_causante'), ['Usuario' => Auth::user()->id]);

            Session::flash('error', trans('mensajes.error.no_pertenece_causante'));

            return redirect()->route('contratos.index')
                ->with(['error' => trans('mensajes.error.no_pertenece_causante')]);
        }

        $tipo_contrato = $adenda->tipo_contrato->nombre;

        if (
            !(($adenda->borrador && Auth::user()->can($tipo_contrato . '-edit-borrador'))
            || (!$adenda->borrador && Auth::user()->can($tipo_contrato . '-edit')))
        ) {
            Session::flash('error', trans('mensajes.error.contrato_borrador'));

            return redirect()->route('contratos.index')
                ->with(['error' => trans('mensajes.error.contrato_borrador')]);
        }

        $tipo_contrato = TipoContrato::whereNombre($tipo_contrato)->first();

        foreach ($this->getDataEditCreate() as $keyVar => $valueVar)
            $$keyVar = $valueVar;

        $contrato = $adenda;
        $repres_tec_eby_old = RepresentanteEby::where('contrato_id', $adenda_id)
            ->pluck('user_id')
            ->toArray();

        return view('contratos.adendas.createEdit', compact(
            'adenda',
            'contrato',
            'tipo_contrato',
            'contrato_padre',
            'isAdenda',
            'causantes',
            'organos',
            'contratistas',
            'monedas', 'estados',
            'repres_tec_eby',
            'repres_tec_eby_old',
            'plazos',
            'no_ejecucion'
        ));
    }

    #endregion

    #region Vistas ajax

    /**
     * @param  int  $contrato_id
     * @param  string  $tipo_contrato
     */
    public function getViews($contrato_id, $tipo_contrato)
    {
        if (
            Auth::user()->cant($tipo_contrato.'-create')
            && Auth::user()->cant($tipo_contrato.'-edit')
        ) {
            Session::flash('error', trans('adendas.sin_permisos'));

            return redirect()->route('contratos.index')
                ->with(['error' => trans('adendas.sin_permisos')]);
        }

        $contrato = Contrato::find($contrato_id);

        if (!$contrato->permite_adendas) {
            Session::flash('error', trans('adendas.sin_permisos'));
            return redirect()->route('contratos.index')
                ->with(['error' => trans('adendas.sin_permisos')]);
        }

        if (!Auth::user()->puedeVerCausante($contrato->causante_id)) {
            Log::error(trans('mensajes.error.no_pertenece_causante'), ['Usuario' => Auth::user()->id]);

            Session::flash('error', trans('mensajes.error.no_pertenece_causante'));
            return redirect()->route('contratos.index')
                ->with(['error' => trans('mensajes.error.no_pertenece_causante')]);
        }

        $contrato_padre = $contrato;
        $contrato = new Contrato();
        $tipo_contrato = TipoContrato::where('nombre', $tipo_contrato)->first();

        $contrato->tipo_id = $tipo_contrato->id;

        foreach ($this->getDataEditCreate() as $keyVar => $valueVar)
            $$keyVar = $valueVar;

        return view("contratos.adendas.forms.index", compact(
            'contrato',
            'tipo_contrato',
            'contrato_padre',
            'isAdenda',
            'causantes',
            'organos',
            'contratistas',
            'monedas',
            'estados',
            'repres_tec_eby',
            'plazos',
            'no_ejecucion'
        ));
    }

    #endregion

    private function getDataEditCreate()
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

        $no_ejecucion = true;
        $isAdenda = true;

        return compact('causantes', 'organos', 'contratistas', 'monedas', 'estados', 'plazos', 'repres_tec_eby',
            'no_ejecucion', 'isAdenda');
    }
}
