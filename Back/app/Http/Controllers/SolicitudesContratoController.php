<?php

namespace App\Http\Controllers;

use Illuminate\Database\QueryException;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

use DB;
use Log;
use Response;
use Storage;
use View;

use App\Role;
use App\User;

use SolicitudContrato\EstadoSolicitudContrato;
use SolicitudContrato\InstanciaSolicitudContrato;
use SolicitudContrato\SolicitudContrato;
use SolicitudContrato\UserContrato;

class SolicitudesContratoController extends Controller {
    public function __construct() {
      $this->middleware('auth', ['except' => 'logout']);
    }

    /**
     * @param  \Illuminate\Http\Request  $request
    */
    public function asociacionesPendientes(Request $request) {
      $input = $request->all();
      $search_input = '';

      $solicitudes_admin = Auth::user()->solicitudes_admin;

      $solicitudes = $solicitudes_admin->filter(function($solicitud) {
          return $solicitud->instancia_actual->esta_pendiente;
      });
      // if($request->getMethod() == "GET") {
      //   $solicitudes = SolicitudContrato::all();//::paginate(config('custom.items_por_pagina'));
      // } else {
      //   $search_input = $input['search_input'];
      //   $solicitudes = SolicitudContrato::where('nombre', 'like', '%' . $input['search_input'] . '%')->paginate(config('custom.items_por_pagina'));
      // }
      $solicitudes = $this->ordenar($solicitudes);
      $estado = 'pendientes';
      return view('contratos.solicitudes.index', compact('solicitudes', 'search_input', 'estado'));
    }

    /**
     * @param  \Illuminate\Http\Request  $request
    */
    public function asociacionesFinalizadas(Request $request) {
      $input = $request->all();
      $search_input = '';

      $solicitudes_admin = Auth::user()->solicitudes_admin;
      $solicitudes = $solicitudes_admin->filter(function($solicitud) {
        return !$solicitud->instancia_actual->esta_pendiente;
      });
      // if($request->getMethod() == "GET") {
      //   $solicitudes = SolicitudContrato::all();//::paginate(config('custom.items_por_pagina'));
      // } else {
      //   $search_input = $input['search_input'];
      //   $solicitudes = SolicitudContrato::where('nombre', 'like', '%' . $input['search_input'] . '%')->paginate(config('custom.items_por_pagina'));
      // }
      $solicitudes = $this->ordenar($solicitudes);
      $estado = 'finalizadas';
      return view('contratos.solicitudes.index', compact('solicitudes', 'search_input', 'estado'));
    }

    /**
     * @param  SolicitudContrato\SolicitudContrato $solicitudes_contrato
    */
    private function ordenar($solicitudes_contrato) {
      $solicitudes_contrato = $solicitudes_contrato->groupBy(function($solicitud_contrato, $key) {
        $um = $this->fechaDeA($solicitud_contrato->ultimo_movimiento, 'd/m/Y', 'm/d/Y');
        return strtotime($um);
      });

      $toArray = array();
      foreach ($solicitudes_contrato as $keySolContrato => $valueSolContrato) {
        $toArray[$keySolContrato] = $valueSolContrato->sortBy(function($solicitud_contrato, $key) {
          return $solicitud_contrato->contrato->expediente_ppal;
        })->all();
      }
      krsort($toArray);

      $ordered = collect();
      foreach ($toArray as $keyArray => $valueArray) {
        foreach ($valueArray as $key => $value) {
          $ordered->push($value);
        }
      }
      return $ordered;
    }

    /**
     * @param  int $id
    */
    public function verSolicitud($id) {
      $solicitud = SolicitudContrato::findOrFail($id);
      if(!Auth::user()->puedeVerCausante($solicitud->causante_id)) {
        return redirect()->route('solicitudes.asociaciones_pendientes');
      }

      return view('contratos.solicitudes.show.index', compact('solicitud'));
    }

    /**
    * @param  \Illuminate\Http\Request  $request
    * @param  int $id
    */
    public function aprobarSolicitud(Request $request, $id) {

      $input = $request->all();
      $rules = array(
          'nro_gde'        => $this->required50(),
      );
      $validator = Validator::make($input, $rules, $this->validationErrorMessages());
      $errores = array();

      if ($validator->fails() || sizeof($errores) > 0) {
        $errores = array_merge($errores, $validator->getMessageBag()->toArray());
        $jsonResponse['status'] = false;
        $jsonResponse['errores'] = $errores;
        $jsonResponse['message'] = [];
        return response()->json($jsonResponse);
      }

      $solicitud = SolicitudContrato::find($id);
      if(!Auth::user()->puedeVerCausante($solicitud->causante_id)) {
        Log::error(trans('mensajes.error.no_pertenece_causante'), ['Usuario' => Auth::user()->id]);
        Session::flash('error', trans('mensajes.error.no_pertenece_causante'));
        $jsonResponse['status'] = false;
        $jsonResponse['message'] = trans('mensajes.error.no_pertenece_causante');
        return response()->json($jsonResponse);
      }

      $id_estado_aprobada = EstadoSolicitudContrato::whereNombre('contratos.estados.solicitud.aprobada')->first()->id;

      try {
        $instancia_solicitud = InstanciaSolicitudContrato::create([
                                    'solicitud_id'    => $id,
                                    'estado_id'       => $id_estado_aprobada,
                                    'user_modifier_id'      => Auth::user()->id
                              ]);
        $instancia_solicitud->gde = $input['nro_gde'];
        $instancia_solicitud->save();
      } catch (QueryException $e) {
        Log::error('QueryException', ['Exception' => $e]);
        Session::flash('error', trans('mensajes.error.insert_db'));
        $jsonResponse['status'] = false;
        $jsonResponse['message'] = trans('mensajes.error.insert_db');
        return response()->json($jsonResponse);
      }

      $contrato = $solicitud->contrato;
      //$contrato->redeterminacion_auto = $solicitud->redeterminacion_auto;

      try {
        $contrato->save();
      } catch (QueryException $e) {
        Log::error('QueryException', ['Exception' => $e]);
        Session::flash('error', trans('mensajes.error.insert_db'));
        $jsonResponse['status'] = false;
        $jsonResponse['message'] = trans('mensajes.error.insert_db');
        return response()->json($jsonResponse);
      }

      $poder = $solicitud->poderes->first();
      $user_contrato_old = UserContrato::whereContratoId($solicitud->contrato_id)
                                       ->whereUserContratistaId($solicitud->user_contratista_id)->first();
      if($poder != null){
        $fecha_fin_poder = $poder->fecha_fin_poder_db;
        $poder_id = $poder->id;
      }else{
        $fecha_fin_poder = null;
        $poder_id = null;}
      try {
        $user_contrato = UserContrato::create([
                                    'contrato_id'           => $solicitud->contrato_id,
                                    'descripcion'           => $solicitud->descripcion,
                                    'user_contratista_id'   => $solicitud->user_contratista_id,
                                    'fecha_fin_poder'       => $fecha_fin_poder,
                                    'poder_id'              => $poder_id,
                                    //'solicitud_id'          => $solicitud->id,
                                    'user_modifier_id'      => Auth::user()->id
                                    ]);
        if($user_contrato_old != null)
          $user_contrato_old->delete();
      } catch (QueryException $e) {
        Log::error('QueryException', ['Exception' => $e]);
        Session::flash('error', trans('mensajes.error.insert_db'));
        $jsonResponse['status'] = false;
        $jsonResponse['message'] = trans('mensajes.error.insert_db');
        return response()->json($jsonResponse);
      }
      //comentar para evitar el envio de notificaciones
    try{
      $solicitud->sendAsociacionGestionadaNotification('contratos.estados.solicitud.aprobada');

      Session::flash('success', trans('mensajes.dato.solicitud_contrato').trans('mensajes.success.aprobada'));
      $jsonResponse['status'] = true;
      $jsonResponse['refresh'] = true;
      $jsonResponse['message'] = trans('mensajes.dato.solicitud_contrato').trans('mensajes.success.aprobada');
      return response()->json($jsonResponse);

    } catch (QueryException $e) {
      Session::flash('success', trans('mensajes.dato.solicitud_contrato').trans('mensajes.success.aprobada'));
      $jsonResponse['status'] = true;
      $jsonResponse['refresh'] = true;
      $jsonResponse['message'] = trans('mensajes.dato.solicitud_contrato').trans('mensajes.success.aprobada');
      return response()->json($jsonResponse);
    }
      
    }

    /**
    * @param  \Illuminate\Http\Request  $request
    * @param  int $id
    */
    public function rechazarSolicitud(Request $request, $id) {
      $solicitud = SolicitudContrato::find($id);
      if(!Auth::user()->puedeVerCausante($solicitud->causante_id)) {
        Log::error(trans('mensajes.error.no_pertenece_causante'), ['Usuario' => Auth::user()->id]);
        Session::flash('error', trans('mensajes.error.no_pertenece_causante'));
        $jsonResponse['status'] = false;
        $jsonResponse['message'] = trans('mensajes.error.no_pertenece_causante');
        return response()->json($jsonResponse);
      }

      $input = $request->all();
      $rules = array(
          'motivo_rechazo'        => $this->required255(),
      );

      $validator = Validator::make($input, $rules, $this->validationErrorMessages());
      $errores = array();

      if ($validator->fails() || sizeof($errores) > 0) {
        $errores = array_merge($errores, $validator->getMessageBag()->toArray());
        $jsonResponse['status'] = false;
        $jsonResponse['errores'] = $errores;
        $jsonResponse['message'] = [];
        return response()->json($jsonResponse);
      }

      $id_estado_rechazada = EstadoSolicitudContrato::whereNombre('contratos.estados.solicitud.rechazada')->first()->id;
      try {
        $instancia_solicitud = InstanciaSolicitudContrato::create([
                                    'solicitud_id'    => $id,
                                    'estado_id'       => $id_estado_rechazada,
                                    'user_modifier_id' => Auth::user()->id
                              ]);
        $instancia_solicitud->motivo_rechazo = $input['motivo_rechazo'];
        $instancia_solicitud->save();
      } catch (QueryException $e) {
        Log::error('QueryException', ['Exception' => $e]);
        Session::flash('error', trans('mensajes.error.insert_db'));
        $jsonResponse['status'] = false;
        $jsonResponse['message'] = trans('mensajes.error.insert_db');
        return response()->json($jsonResponse);
      }

      $solicitud = SolicitudContrato::find($id);
      $user_contratista = $solicitud->user_publico;

      $user_contrato = UserContrato::whereUserContratistaId($user_contratista->id)
                                         ->whereContratoId($solicitud->contrato_id)->first();
      if($user_contrato != null)
        $user_contrato->delete();


        try{
          $solicitud->sendAsociacionGestionadaNotification('contratos.estados.solicitud.rechazada');

          Session::flash('success', trans('mensajes.dato.solicitud_contrato').trans('mensajes.success.rechazada'));
          $jsonResponse['message'] = trans('mensajes.dato.solicitud_contrato').trans('mensajes.success.rechazada');
          $jsonResponse['status'] = true;
          $jsonResponse['refresh'] = true;
          return response()->json($jsonResponse);
    
        } catch (QueryException $e) {
          Session::flash('success', trans('mensajes.dato.solicitud_contrato').trans('mensajes.success.rechazada'));
          $jsonResponse['message'] = trans('mensajes.dato.solicitud_contrato').trans('mensajes.success.rechazada');
          $jsonResponse['status'] = true;
          $jsonResponse['refresh'] = true;
          return response()->json($jsonResponse);
        }

      

    }

    /**
    * @param  \Illuminate\Http\Request  $request
    * @param  string  $estado
    */
    public function exportar(Request $request, $estado) {
      $input = $request->all();
      $filtro = $input['excel_input'];

      $solicitudes_admin = Auth::user()->solicitudes_admin;

      if($estado == 'pendientes') {
        $solicitudes = $solicitudes_admin->filter(function($solicitud) {
          if($solicitud->instancia_actual->esta_pendiente)
            return true;
        });
      } else {
        $solicitudes = $solicitudes_admin->filter(function($solicitud) {
          if(!$solicitud->instancia_actual->esta_pendiente)
            return true;
        });
      }
      $solicitudes = $this->ordenar($solicitudes);

      $solicitudes = $solicitudes->map(function ($solicitud, $key) use($estado) {
          $arr = [
              trans('forms.numeral') => $solicitud->nro_solicitud,
              trans('forms.fecha_solicitud_th') => $solicitud->created_at,
              trans('forms.expediente_madre') => $solicitud->expediente_madre,
              trans('forms.contratista') => $solicitud->user_publico->nombre_apellido_documento,
          ];

          if(!Auth::user()->usuario_causante)
            $arr[trans('forms.causante')] = $solicitud->causante_nombre_color['nombre'];

          if($estado == 'finalizadas')
            $arr[trans('forms.estado')] = $solicitud->estado_nombre_color['nombre'];

          $arr[trans('forms.ultimo_movimiento_th')] = $solicitud->ultimo_movimiento;

          return $arr;
      });

      return $this->toExcel(trans('forms.asociaciones_' . $estado),
                            $this->filtrarExportacion($solicitudes, $filtro));
    }

}
