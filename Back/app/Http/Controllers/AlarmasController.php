<?php

namespace App\Http\Controllers;

use Illuminate\Database\QueryException;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

use DB;
use Hash;
use Log;
use Redirect;
use Response;
use View;

use AlarmaSolicitud\AlarmaSolicitud;
use AlarmaSolicitud\TipoDesencadenante;
use SolicitudRedeterminacion\Instancia\TipoInstanciaRedet;

use App\Role;
use App\User;

use Yacyreta\Causante;

use App\Events\PasosSolicitudRedeterminacion;

class AlarmasController extends Controller {

    public function __construct() {
      View::share('ayuda', 'configuracion');
      $this->middleware('auth', ['except' => 'logout']);
    }

    public function indexSolicitud() {
      $alarmas = AlarmaSolicitud::all();
      $alarmas = $this->paginateCustom($alarmas);
      $search_input = '';
      return view('alarmas.solicitud.index', compact('alarmas', 'search_input'));
    }

    public function createSolicitud() {
      $opciones['estados'] = TipoInstanciaRedet::all()->pluck('modelo', 'id')->prepend(trans('forms.select.estado'), '');
      $opciones['acciones'] = TipoInstanciaRedet::all()->pluck('modelo', 'id')->prepend(trans('forms.select.accion'), '');
      // $opciones['acciones_correccion'] = TipoInstancia::where('orden', '!=', null)->where('modelo', '!=', 'Iniciada')->get()->pluck('modelo', 'id');
      $opciones['roles'] = Role::all()->pluck('name', 'id')->prepend(trans('forms.select.rol'), '');
      $opciones['destinatarios'] = User::all()->pluck('nombre', 'id')->prepend(trans('forms.select.destinatario'), '');
      $opciones['causantes'] = Causante::all()->pluck('nombre', 'id')->prepend(trans('forms.causante_responsable'), 'responsable')->prepend(trans('forms.select.causante'), '');
      $tipos_desencadenante['accion'] = TipoDesencadenante::whereNombre('accion')->first()->id;
      $tipos_desencadenante['estado'] = TipoDesencadenante::whereNombre('estado')->first()->id;

      return view('alarmas.solicitud.create', compact('opciones', 'tipos_desencadenante'));
    }

    /**
    * @param Request $request
    */
    public function updateSolicitud(Request $request) {
      $input = $request->all();
      $rules = array(
          'nombre'              => $this->required50(),
          'titulo'              => $this->requiredN(30),
          'mensaje'             => $this->requiredFile(),
      );
      $validator = Validator::make($input, $rules, $this->validationErrorMessages());

      $errores = array();
      $errores = array_merge($errores, $validator->getMessageBag()->toArray());

      if ($validator->fails() || sizeof($errores) > 0) {
        $errores = array_merge($errores, $validator->getMessageBag()->toArray());
        $jsonResponse['status'] = false;
        $jsonResponse['errores'] = $errores;
        $jsonResponse['message'] = [];
        return response()->json($jsonResponse);
      }

      $input['user_creator_id'] = Auth::user()->id;
      if($input['causante_id'] == 'responsable') {
        $input['causante_id'] = null;
        $input['responsable_contrato'] = 1;
      }

      $tipos_desencadenante_estado = TipoDesencadenante::whereNombre('estado')->first()->id;
      if($input['tipo_desencadenante_id'] == $tipos_desencadenante_estado)
        $input['desencadenante_id'] = $input['desencadenante_estado_id'];

      try {
        $alarma = AlarmaSolicitud::create($input);
      } catch(\QueryException $e) {
        Log::error('QueryException', ['Exception' => $e]);
        Session::flash('error', trans('mensajes.error.insert_db'));
        $response['status'] = false;
        $response['message'] = [trans('mensajes.error.insert_db')];
        return $response;
      }
      
      Session::flash('success', trans('mensajes.dato.alarma') . trans('mensajes.success.creada'));
      $jsonResponse['message'] = [trans('mensajes.dato.alarma') . trans('mensajes.success.creada')];
      $jsonResponse['status'] = true;
      $jsonResponse['url'] = route('alarmas.solicitud');
      return response()->json($jsonResponse);
    }

    /**
    * @param int $id
    */
    public function editSolicitud($id) {
      $alarma = AlarmaSolicitud::findOrFail($id);

      $opciones['estados'] = TipoInstanciaRedet::all()->pluck('modelo', 'id')->prepend(trans('forms.select.estado'), '');
      $opciones['acciones'] = TipoInstanciaRedet::all()->pluck('modelo', 'id')->prepend(trans('forms.select.accion'), '');
      // $opciones['acciones_correccion'] = TipoInstancia::where('orden', '!=', null)->where('modelo', '!=', 'Iniciada')->get()->pluck('modelo', 'id');
      $opciones['roles'] = Role::all()->pluck('name', 'id')->prepend(trans('forms.select.rol'), '');
      $opciones['destinatarios'] = User::all()->pluck('nombre', 'id')->prepend(trans('forms.select.destinatario'), '');
      $opciones['causantes'] = Causante::all()->pluck('nombre', 'id')->prepend(trans('forms.causante_responsable'), 'responsable')->prepend(trans('forms.select.causante'), '');
      $tipos_desencadenante['accion'] = TipoDesencadenante::whereNombre('accion')->first()->id;
      $tipos_desencadenante['estado'] = TipoDesencadenante::whereNombre('estado')->first()->id;

      return view('alarmas.solicitud.edit', compact('alarma', 'opciones', 'tipos_desencadenante'));
    }

    /**
    * @param Request $request
    * @param id $id
    */
    public function editSolicitudPost(Request $request, $id) {
      $input = $request->all();
      $rules = array(
          'nombre'              => $this->required50(),
          'titulo'              => $this->requiredN(30),
          'mensaje'             => $this->requiredFile(),
      );

      $validator = Validator::make($input, $rules, $this->validationErrorMessages());

      $errores = array();
      $errores = array_merge($errores, $validator->getMessageBag()->toArray());

      if ($validator->fails() || sizeof($errores) > 0) {
        $errores = array_merge($errores, $validator->getMessageBag()->toArray());
        $jsonResponse['status'] = false;
        $jsonResponse['errores'] = $errores;
        $jsonResponse['$input'] = $input;
        $jsonResponse['message'] = [];
        return response()->json($jsonResponse);
      }

      $alarma = AlarmaSolicitud::findOrFail($id);
      $alarma->nombre = $input['nombre'];
      $alarma->usuario_sistema = $input['usuario_sistema'];
      $alarma->destinatario_id = $input['destinatario_id'];

      if($input['causante_id'] == 'responsable') {
        $input['causante_id'] = null;
        $input['responsable_contrato'] = 1;
      } else {
        $input['responsable_contrato'] = 0;
      }
      $alarma->causante_id = $input['causante_id'];

      $alarma->responsable_contrato = $input['responsable_contrato'];
      $alarma->role_id = $input['role_id'];

      $alarma->tipo_desencadenante_id = $input['tipo_desencadenante_id'];
      if($input['tipo_desencadenante_id'] == 2) {
        $alarma->desencadenante_id = $input['desencadenante_estado_id'];
      } else {
        $alarma->desencadenante_id = $input['desencadenante_id'];
      }

      if($input['accion_estado'] == 0) {
         $alarma->tiempo_espera = null;
      } else {
         $alarma->tiempo_espera = $input['tiempo_espera'];
      }

      $alarma->titulo = $input['titulo'];
      $alarma->mensaje = $input['mensaje'];
      try {
        $alarma->save();
      } catch(\QueryException $e) {
        Log::error('QueryException', ['Exception' => $e]);
        Session::flash('error', trans('mensajes.error.insert_db'));
        $response['status'] = false;
        $response['message'] = [trans('mensajes.error.insert_db')];
        return $response;
      }

      Session::flash('success', trans('mensajes.dato.alarma') . trans('mensajes.success.editada'));
      $jsonResponse['message'] = [trans('mensajes.dato.alarma') . trans('mensajes.success.editada')];
      $jsonResponse['status'] = true;
      $jsonResponse['url'] = route('alarmas.solicitud');
      return response()->json($jsonResponse);
    }


    /**
    * @param int $id
    */
    public function showSolicitud($id) {
      $alarma = AlarmaSolicitud::findOrFail($id);
      return view('alarmas.solicitud.show', compact('alarma'));
    }

    /**
    * @param int $id
    */
    public function habilitar($id) {
      $alarma = AlarmaSolicitud::find($id);
      $alarma->habilitada = 1;

      try {
        $alarma->save();
      } catch (QueryException $e) {
        Log::error('QueryException', ['Exception' => $e]);
        Session::flash('error', trans('mensajes.error.insert_db'));
        $jsonResponse['status'] = false;
        $jsonResponse['message'] = trans('mensajes.error.insert_db');
        return response()->json($jsonResponse);
      }

      Session::flash('success', trans('mensajes.dato.alarma').trans('mensajes.success.habilitada'));
      $jsonResponse['status'] = true;
      $jsonResponse['refresh'] = true;
      $jsonResponse['message'] = trans('mensajes.dato.alarma').trans('mensajes.success.habilitada');
      return response()->json($jsonResponse);
    }

    /**
    * @param int $id
    */
    public function deshabilitar($id) {
      $alarma = AlarmaSolicitud::find($id);
      $alarma->habilitada = 0;

      try {
        $alarma->save();
      } catch (QueryException $e) {
        Log::error('QueryException', ['Exception' => $e]);
        Session::flash('error', trans('mensajes.error.insert_db'));
        $jsonResponse['status'] = false;
        $jsonResponse['message'] = trans('mensajes.error.insert_db');
        return response()->json($jsonResponse);
      }

      Session::flash('success', trans('mensajes.dato.alarma').trans('mensajes.success.deshabilitada'));
      $jsonResponse['status'] = true;
      $jsonResponse['refresh'] = true;
      $jsonResponse['message'] = trans('mensajes.dato.alarma').trans('mensajes.success.deshabilitada');
      return response()->json($jsonResponse);
    }
}
