<?php

namespace App\Http\Controllers\Contratos;

use Contrato\Ampliacion\Ampliacion;
use Contrato\Ampliacion\TipoAmpliacion;
use Contrato\Contrato;
use Contrato\ContratoAdjunto;
use Contrato\MotivoReprogramacion;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Log;
use View;

class AmpliacionController extends ContratosControllerExtended
{

    public function __construct()
    {
        View::share('ayuda', 'contrato');
        $this->middleware('auth', ['except' => 'logout']);
    }

#region Creacion de Ampliacion

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

        if (!$contrato->permite_ampliaciones_de_obra) {
            Session::flash('error', trans('ampliaciones.sin_permisos'));
            return redirect()->route('contratos.index')
                ->with(['error' => trans('ampliaciones.sin_permisos')]);
        }

        foreach (TipoAmpliacion::all() as $keyTipo => $valueTipo) {
            $nombre = $valueTipo->nombre;
            $metodo = 'permite_'.$nombre;
            if ($contrato->$metodo) {
                $select_options[$valueTipo->id] = $nombre;
            }
        }

        if (!isset($select_options)) {
            Session::flash('error', trans('ampliaciones.sin_permisos'));
            return redirect()->route('contratos.index')
                ->with(['error' => trans('ampliaciones.sin_permisos')]);
        }

        $ampliacion = new Ampliacion();
        $ampliacion->contrato_id = $contrato_id;

        return view('contratos.ampliaciones.createEdit', compact('select_options', 'contrato_id', 'ampliacion'));
    }

    /**
     * @param  string  $required
     */
    public function getRules($required)
    {
        return [
            'expediente' => $required.$this->min3maxN(50),
            'resoluc_aprobatoria' => $required.$this->min3maxN(50),
            'motivo_id' => $required,
            'observaciones' => 'nullable|'.$this->min3maxN(1000),
        ];
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     */
    public function storeUpdate(Request $request)
    {
        $input = $request->except(['_token']);

        $contrato = Contrato::find($input['contrato_id']);

        if (!Auth::user()->puedeVerCausante($contrato->causante_id)) {
            $jsonResponse['status'] = false;
            Session::flash('error', trans('mensajes.error.no_pertenece_causante'));
            $jsonResponse['url'] = route('contratos.index');
            $jsonResponse['message'] = [trans('mensajes.error.no_pertenece_causante')];
            return response()->json($jsonResponse);
        }

        if (!$contrato->permite_ampliaciones_de_obra) {
            $jsonResponse['status'] = false;
            Session::flash('error', trans('ampliaciones.no_se_puede_crear'));
            $jsonResponse['url'] = route('contratos.index');
            $jsonResponse['message'] = [trans('ampliaciones.no_se_puede_crear')];
            return response()->json($jsonResponse);
        }

        if ($input['id'] != null) {
            $input['tipo_id'] = Ampliacion::find($input['id'])->tipo_id;
        }

        $tipo_ampliacion = TipoAmpliacion::find($input['tipo_id'])->nombre;
        if (Auth::user()->cant($tipo_ampliacion.'-create')) {
            $jsonResponse['status'] = false;
            Session::flash('error', trans('ampliaciones.sin_permisos'));
            $jsonResponse['url'] = route('contratos.index');
            $jsonResponse['message'] = [trans('ampliaciones.sin_permisos')];
            return response()->json($jsonResponse);
        }

        $jsonResponse = $this->$tipo_ampliacion($input);
        $jsonResponse = $jsonResponse->getData();

        if ($jsonResponse->status) {
            $ampliacion = Ampliacion::find($jsonResponse->ampliacion_id);
            if (isset($input['adjunto']) && $request->hasFile('adjunto')) {
                $ampliacion->adjuntos->each->delete();
                $className = $ampliacion->getClassName();
                $id = $ampliacion->id;
                $contrato_id = $ampliacion->contrato_id;
                foreach ($input['adjunto'] as $keyAdjunto => $valueAdjunto) {
                    $adjuntos_json = $this->uploadFile($request, $ampliacion->id, 'adjunto|'.$keyAdjunto,
                        'contrato/'.$contrato_id.'/'.$tipo_ampliacion);
                    ContratoAdjunto::create([
                        'clase_id' => $id,
                        'clase_type' => $className,
                        'adjunto' => $adjuntos_json,
                    ]);
                }
            }
        }

        return response()->json($jsonResponse);
    }

    /**
     * @param  array  $input
     */
    public function ampliacion($input)
    {
        $borrador = $input['borrador'];
        if ($borrador) {
            $required = 'nullable|';
        } else {
            $required = 'required|';
        }

        $errores = array();
        $rules = $this->getRules($required);
        $rules['plazo'] = $required;

        if (!$borrador && $input['plazo'] != null) {
            $meses = $input['plazo'];
            $contrato_padre = Contrato::select('id', 'fecha_acta_inicio')->find($input['contrato_id']);
            $es_primero = (int) explode('/', $contrato_padre->fecha_acta_inicio)[0] == 1;
            if (!$es_primero) {
                $meses = $meses + 1;
            } else {
                $meses = $meses;
            }

            if ($contrato_padre->mes_proximo_certificado > $meses) {
                $errores['plazo'] = trans('validation_custom.meses_mayor_mes_ultima_certificacion');
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

        if ($input['id'] == null) {
            $ampliacion = new Ampliacion($input);
            $ampliacion->save();
        } else {
            $ampliacion = Ampliacion::find($input['id']);
            $ampliacion->update($input);
        }

        if (!$borrador) {
            $this->store($ampliacion->id);
        }

        if (!$borrador) {
            Session::flash('success', trans('mensajes.dato.ampliacion').trans('mensajes.success.guardada'));
            $jsonResponse['message'] = [trans('mensajes.dato.ampliacion').trans('mensajes.success.guardada')];
        } elseif ($input['id'] == null) {
            Session::flash('success', trans('mensajes.dato.ampliacion').trans('mensajes.success.creada'));
            $jsonResponse['message'] = [trans('mensajes.dato.ampliacion').trans('mensajes.success.creada')];
        } else {
            Session::flash('success', trans('mensajes.dato.ampliacion').trans('mensajes.success.editada'));
            $jsonResponse['message'] = [trans('mensajes.dato.ampliacion').trans('mensajes.success.editada')];
        }

        $jsonResponse['status'] = true;
        $jsonResponse['ampliacion_id'] = $ampliacion->id;
        $jsonResponse['url'] = route('contratos.ver.incompleto',
            ['id' => $input['contrato_id'], 'accion' => 'ampliacion']);
        return response()->json($jsonResponse);
    }

    /**
     * @param  array  $input
     */
    public function reprogramacion($input)
    {
        $borrador = $input['borrador'];
        if ($borrador) {
            $required = 'nullable|';
        } else {
            $required = 'required|';
        }

        $rules = $this->getRules($required);

        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {
            $jsonResponse['status'] = false;
            $jsonResponse['errores'] = $validator->getMessageBag()->toArray();
            Session::flash('error', trans('mensajes.error.revisar'));
            $jsonResponse['message'] = [trans('mensajes.error.revisar')];
            return response()->json($jsonResponse);
        }

        if ($input['id'] == null) {
            $ampliacion = new Ampliacion($input);
            $ampliacion->save();
        } else {
            $ampliacion = Ampliacion::find($input['id']);
            $ampliacion->update($input);
        }

        if (!$borrador) {
            $this->store($ampliacion->id);
        }

        if (!$borrador) {
            Session::flash('success', trans('mensajes.dato.reprogramacion').trans('mensajes.success.guardada'));
            $jsonResponse['message'] = [trans('mensajes.dato.reprogramacion').trans('mensajes.success.guardada')];
        } elseif ($input['id'] == null) {
            Session::flash('success', trans('mensajes.dato.reprogramacion').trans('mensajes.success.creada'));
            $jsonResponse['message'] = [trans('mensajes.dato.reprogramacion').trans('mensajes.success.creada')];
        } else {
            Session::flash('success', trans('mensajes.dato.reprogramacion').trans('mensajes.success.editada'));
            $jsonResponse['message'] = [trans('mensajes.dato.reprogramacion').trans('mensajes.success.editada')];
        }

        $jsonResponse['status'] = true;
        $jsonResponse['ampliacion_id'] = $ampliacion->id;
        $jsonResponse['url'] = route('contratos.ver.incompleto',
            ['id' => $input['contrato_id'], 'accion' => 'reprogramacion']);
        return response()->json($jsonResponse);
    }

#endregion

#region Edicion de Ampliacion

    /**
     * @param  int  $id
     */
    public function edit($id)
    {
        $ampliacion = Ampliacion::findOrFail($id);

        $contrato_padre = $ampliacion->contrato;

        if (!Auth::user()->puedeVerCausante($contrato_padre->causante_id)) {
            Log::error(trans('mensajes.error.no_pertenece_causante'), ['Usuario' => Auth::user()->id]);

            Session::flash('error', trans('mensajes.error.no_pertenece_causante'));
            return redirect()->route('contratos.index')
                ->with(['error' => trans('mensajes.error.no_pertenece_causante')]);
        }

        if (!$ampliacion->borrador) {
            Session::flash('error', trans('mensajes.error.contrato_borrador'));
            return redirect()->route('contratos.index')
                ->with(['error' => trans('mensajes.error.contrato_borrador')]);
        }

        $tipo_ampliacion = $ampliacion->tipo_ampliacion->nombre;

        if (!Auth::user()->can($tipo_ampliacion.'-create')) {
            Session::flash('error', trans('ampliaciones.sin_permisos'));
            return redirect()->route('contratos.index')
                ->with(['error' => trans('ampliaciones.sin_permisos')]);
        }

        foreach ($this->getDataEditCreate() as $keyVar => $valueVar) {
            $$keyVar = $valueVar;
        }

        return view('contratos.ampliaciones.createEdit', compact(
            'select_options',
            'contrato_id',
            'ampliacion',
            'motivos',
            'tipo_ampliacion'
        ));
    }

    #endregion Edicion de Ampliacion ////////////

    /**
     * @param  int  $id
     */
    public function store($id)
    {
        $ampliacion = Ampliacion::find($id);

        $contrato = $ampliacion->contrato;
        $meses = $ampliacion->meses_cronograma;
        foreach ($contrato->contratos_monedas as $keyContratoMoneda => $valueContratoMoneda) {
            // Duplico ContratoMoneda de contrato
            $newContratoMoneda = $valueContratoMoneda->replicate();
            $newContratoMoneda->clase_type = $ampliacion->getClassName();
            $newContratoMoneda->clase_id = $ampliacion->id;

            $newContratoMoneda->save();

            // "Elimino" el vigente
            $newContratoMoneda->itemizado_id = $newContratoMoneda->itemizado_actual->id;
            $newContratoMoneda->itemizado_vigente_id = null;
            $newContratoMoneda->save();

            // Duplico Itemizado
            $itemizado = $valueContratoMoneda->itemizado_actual;
            $newItemizado = $itemizado->replicate();
            $newItemizado->save();
            $newItemizado_id = $newItemizado->id;

            // Actualizo Itemizado en ContratoMoneda
            $newContratoMoneda->itemizado_id = $newItemizado_id;
            $newContratoMoneda->save();

            // Duplico cronograma de Itemizado del contrato
            $cronograma = $itemizado->cronograma;

            $newCronograma = $cronograma->replicate();
            $newCronograma->itemizado_id = $newItemizado_id;
            $newCronograma->meses = $meses;
            $newCronograma->borrador = 1;
            $newCronograma->save();
            $cronograma_id = $newCronograma->id;

            // Duplico Items de Cronograma
            $this->duplicateItems($itemizado->items_nivel_1, $newItemizado_id, $cronograma_id, $meses, false, null);
        }

        $this->createInstanciaHistorial($ampliacion, 'cronograma', 'borrador');

        return "OK";
    }

#region Ver Ampliacion

    /**
     * @param  int  $id
     * @param  string  $accion  |nullable
     */
    public function ver($id, $accion = null)
    {
        // La variable se llama $contrato en vez de $ampliacion
        // para poder reusar la logica de contratos
        $contrato = Ampliacion::findOrFail($id);

        $contrato_padre = $contrato->contrato;

        if (!Auth::user()->puedeVerCausante($contrato_padre->causante_id))
            return redirect()->route('contratos.index');

        $tipo_ampliacion = $contrato->tipo_ampliacion->nombre;

        if (!Auth::user()->can($tipo_ampliacion . '-create')) {
            Session::flash('error', trans('ampliaciones.sin_permisos'));
            return redirect()->route('contratos.index')
                ->with(['error' => trans('ampliaciones.sin_permisos')]);
        }

        $contratoIncompleto = $contrato->incompleto;

        $opciones['version'] = 'vigente';
        $opciones['visualizacion'] = 'porcentaje';

        return view('contratos.ampliaciones.show.index',
            compact(
                'contrato_id',
                'contrato',
                'contratoIncompleto',
                'accion',
                'opciones'
            ));
    }

    /**
     * @param  int  $id
     * @param  string  $accion  | nullable
     */
    public function verEditar($id, $accion = null)
    {
        if ($accion == 'cronograma' && Auth::user()->cant('cronograma-manage')) {
            return redirect()->route('contratos.index')
                ->with(['error' => trans('mensajes.error.contrato_borrador')]);
        }

        return $this->ver($id, $accion);
    }

#endregion

#region Vistas ajax

    /**
     * @param  int  $contrato_id
     * @param  string  $tipo_id
     */
    public function getViews($contrato_id, $tipo_id)
    {
        $tipo_ampliacion = TipoAmpliacion::find($tipo_id)->nombre;

        if (Auth::user()->cant($tipo_ampliacion.'-create') && Auth::user()->cant($tipo_ampliacion.'-edit')) {
            $jsonResponse['status'] = false;
            Session::flash('error', trans('ampliaciones.sin_permisos'));
            $jsonResponse['url'] = route('contratos.index');
            $jsonResponse['message'] = [trans('ampliaciones.sin_permisos')];
            return response()->json($jsonResponse);
        }

        $contrato = Contrato::find($contrato_id);

        if (!$contrato->permite_ampliaciones_de_obra) {
            $jsonResponse['status'] = false;
            Session::flash('error', trans('ampliaciones.sin_permisos'));
            $jsonResponse['url'] = route('contratos.index');
            $jsonResponse['message'] = [trans('ampliaciones.sin_permisos')];
            return response()->json($jsonResponse);
        }

        foreach ($this->getDataEditCreate() as $keyVar => $valueVar) {
            $$keyVar = $valueVar;
        }

        $ampliacion = new Ampliacion();
        $ampliacion->contrato_id = $contrato_id;

        $jsonResponse['status'] = true;
        $jsonResponse['view'] = View::make("contratos.ampliaciones.forms.index",
            compact('contrato', 'ampliacion', 'motivos', 'tipo_ampliacion'))->render();
        return response()->json($jsonResponse);
    }

    /**
     * @param  int  $id
     * @param  string  $seccion
     * @param  string  $visualizacion  | nullable
     */
    public function getViewsCronograma($id, $seccion, $visualizacion = 'porcentaje')
    {

        if ($seccion == 'cronograma' && Auth::user()->cant('cronograma-manage')) {
            return redirect()->route('contratos.index')
                ->with(['error' => trans('mensajes.error.contrato_borrador')]);
        }
        $contrato = Ampliacion::findOrFail($id);

        if (!Auth::user()->puedeVerCausante($contrato->contrato->causante_id)) {
            return redirect()->route('contratos.index');
        }
        $contratoIncompleto = $contrato->incompleto;

        if (!$contrato->completo || (!$contrato->completo && !$contrato->incompleto['$seccion'] && Auth::user()->can($seccion.'-manage'))) {
            $visualizacion = 'all';
        }

        $opciones['version'] = 'original';
        $opciones['visualizacion'] = $visualizacion;
        $fromAjax = true;

        $jsonResponse['view'] = View::make("contratos.contratos.show.{$seccion}.index",
            compact('contrato', 'contratoIncompleto', 'accion', 'indices', 'opciones', 'fromAjax', 'unidadesMedida',
                'publicados', 'responsables'))->render();

        $metodo = 'has_'.$seccion.'_vigente';
        if ($opciones['version'] == 'vigente' && $contrato->$metodo) {
            $id = $contrato->id;
        }
        // $id = $contrato->adenda_vigente_id;

        $jsonResponse['historial'] = route('contrato.historial',
            ['clase_id' => $id, 'clase_type' => $contrato->getClassNameAsKey(), 'seccion' => $seccion]);
        return response()->json($jsonResponse);

    }

#endregion

    private function getDataEditCreate()
    {
        $motivos = MotivoReprogramacion::getOpciones();

        return compact('motivos');
    }

#region Eliminar Ampliacion

    /**
     * @param  int  $id
     */
    public function preDelete($id)
    {
        $ampliacion = Ampliacion::find($id);

        if (Auth::user()->cant($ampliacion->tipo_ampliacion->nombre.'-delete')) {
            $jsonResponse['status'] = false;
            $jsonResponse['title'] = trans('index.eliminar').' '.trans('contratos.tipo_ampliacion.'.$ampliacion->tipo_ampliacion->nombre);

            $jsonResponse['message'] = [trans('mensajes.error.permisos')];
            return response()->json($jsonResponse);
        }

        if (!$ampliacion->borrador) {
            $jsonResponse['status'] = false;
            $jsonResponse['title'] = trans('index.eliminar').' '.trans('contratos.tipo_ampliacion.'.$ampliacion->tipo_ampliacion->nombre);
            $jsonResponse['message'] = [trans('index.no_puede_eliminar.'.$ampliacion->tipo_ampliacion->nombre)];

            return response()->json($jsonResponse);
        } else {
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

        $ampliacion = Ampliacion::find($id);

        try {
            $ampliacion->delete();

        } catch (QueryException $e) {
            Log::error('QueryException', ['Exception' => $e]);
            $jsonResponse['status'] = false;
            $jsonResponse['message'] = [trans('mensajes.error.insert_db')];
            return response()->json($jsonResponse);
        }

        $jsonResponse['status'] = true;
        $jsonResponse['refresh'] = true;
        Session::flash('success',
            trans('contratos.tipo_ampliacion.'.$ampliacion->tipo_ampliacion->nombre).trans('mensajes.success.eliminada'));
        $jsonResponse['message'] = [trans('contratos.tipo_ampliacion.'.$ampliacion->tipo_ampliacion->nombre).trans('mensajes.success.eliminada')];

        return response()->json($jsonResponse);
    }

#endregion

}
