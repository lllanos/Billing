<?php

namespace App\Http\Controllers;

use Illuminate\Database\QueryException;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

use DB;
use Hash;
use Log;
use Redirect;
use Response;
use Storage;
use View;

use Contrato\Contrato;
use SolicitudContrato\EstadoSolicitudContrato;
use SolicitudContrato\InstanciaSolicitudContrato;
use SolicitudContrato\SolicitudContrato;
use SolicitudContrato\UserContrato;
use SolicitudContrato\Poder;

use CalculoRedeterminacion\VariacionMesPolinomica;
use CalculoRedeterminacion\CalculoModelExtended;

class ContratosController extends Controller {

    public function __construct() {
      View::share('ayuda', 'contratos');
      $this->middleware('auth', ['except' => 'logout']);
    }

    ////////////////// Contratos //////////////////
    /**
     * @param  \Illuminate\Http\Request  $request
    */
    public function misContratos(Request $request) {
      $input = $request->all();
      $search_input = '';

      $user_publico = Auth::user()->user_publico;
      $user_contratos =  $user_publico->contratos;
      $user_contratos = $this->ordenar($user_contratos);

      if($request->getMethod() != "GET") {
        $search_input = $input['search_input'];
        $input_lower = $this->minusculaSinAcentos($input['search_input']);

        if($input_lower != '') {
          $user_contratos = $user_contratos->filter(function($user_contrato) use($input_lower) {
            return
              substr_count($this->minusculaSinAcentos($user_contrato->expedientes), $input_lower) > 0 ||
              substr_count($user_contrato->fecha_licitacion, $input_lower) > 0 ||
              substr_count($this->minusculaSinAcentos($user_contrato->contrato->contratista->nombre_cuit), $input_lower) > 0 ||
              substr_count($this->minusculaSinAcentos($user_contrato->nombre), $input_lower) > 0 ;
          });
        }
      }
      $user_contratos = $this->paginateCustom($user_contratos);

      return view('contratos.contratos.index', compact('user_contratos', 'search_input'));
    }

    /**
     * @param  SolicitudContrato\UserContrato $user_contratos
    */
    private function ordenar($user_contratos) {
      $user_contratos = $user_contratos->groupBy(function($user_contrato, $key) {
        if($user_contrato->contrato->ultimo_salto == null)
          return null;
        else
          return $user_contrato->contrato->ultimo_salto->publicacion_id;
      });

      $toArray = array();
      foreach ($user_contratos as $keyUserContrato => $valueUserContrato) {
        $toArray[$keyUserContrato] = $valueUserContrato->sortBy(function($user_contrato, $key) {
          return $user_contrato->expedientes;
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
    public function verContrato($id) {
      $user_contrato = UserContrato::findOrFail($id);
      $contratista_id = $user_contrato->user_contratista_id;
      $contratista_id = $user_contrato->user_contratista_id;
      if($contratista_id != Auth::user()->user_contratista_id)
        return redirect()->route('contratos.index');

      return view('contratos.contratos.show.index', compact('user_contrato'));
    }

    /**
     * @param  \Illuminate\Http\Request  $request
    */
    public function exportarContratos(Request $request) {
      $input = $request->all();
      $filtro = $input['excel_input'];

      $user_publico = Auth::user()->user_publico;
      $user_contratos =  $user_publico->contratos;

      $user_contratos = $this->ordenar($user_contratos);

      $user_contratos = $user_contratos->map(function ($user_contrato, $key) {
          $arr = array();

          $arr[trans('forms.expedient_ppal_elec')] = $user_contrato->expedientes;
          $arr[trans('forms.fecha_licitacion')] = $user_contrato->contrato->fecha_licitacion;
          $arr[trans('forms.descripcion')] = $user_contrato->descripcion;
          $arr[trans('forms.contratista')] = $user_contrato->contrato->contratista->nombre_cuit;
          $vr = '';
          $separador_vr = ' - ';
          foreach($user_contrato->contrato->obras as $keyObra => $valueObra) {
            if($valueObra->ultima_variacion != null)
              $vr .=   '' . $valueObra->ultima_variacion->variacion_show . $separador_vr;
            else
            $vr .= '1' . $separador_vr;
          }
          if(substr($vr, -3) == $separador_vr)
            $vr = substr($vr, 0, -3);

          $arr[trans('forms.vr')] = $vr;
          $arr[trans('forms.ultimo_salto')] = $user_contrato->contrato->ultimo_salto_my;
          $arr[trans('forms.ultima_solicitud')] = $user_contrato->contrato->ultima_solicitud;
          return $arr;
      });

      return $this->toExcel(trans('index.mis') . trans('index.contratos'),
                            $this->filtrarExportacion($user_contratos, $filtro));
    }
    ////////////////// END Contratos //////////////////

    ////////////////// Solicitudes //////////////////
    public function solicitudes(Request $request) {
      $input = $request->all();
      $search_input = '';

      $user_publico = Auth::user()->user_publico;
      $solicitudes_contrato = $user_publico->solicitudes_contrato;

      if($request->getMethod() != "GET") {
        $search_input = $input['search_input'];
        $input_lower = $this->minusculaSinAcentos($input['search_input']);

        if($input_lower != '') {
          $solicitudes_contrato = $solicitudes_contrato->filter(function($solicitud_contrato) use($input_lower) {
            return
              substr_count($this->minusculaSinAcentos($solicitud_contrato->created_at), $input_lower) > 0 ||
              substr_count($this->minusculaSinAcentos($solicitud_contrato->expedientes), $input_lower) > 0 ||
              substr_count($this->minusculaSinAcentos($solicitud_contrato->estado_nombre_color['nombre']), $input_lower) > 0 ||
              substr_count($this->minusculaSinAcentos($solicitud_contrato->ultimo_movimiento), $input_lower) > 0 ||
              substr_count($this->minusculaSinAcentos($solicitud_contrato->descripcion), $input_lower) > 0 ;
          });
        }
      }

      $solicitudes_contrato = $this->ordenarSolicitudes($solicitudes_contrato);
      $solicitudes_contrato = $this->paginateCustom($solicitudes_contrato);
      return view('contratos.solicitudes.index', compact('solicitudes_contrato', 'search_input'));
    }

    /**
     * @param  SolicitudContrato\SolicitudContrato $solicitudes_contrato
    */
    private function ordenarSolicitudes($solicitudes_contrato) {
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

    public function asociar() {
      $traduccion_ddjj = 'contratos.solicitar';

      return view('contratos.contratos.asociar', compact('traduccion_ddjj'));
    }

    /**
     * @param  \Illuminate\Http\Request  $request
    */
    public function updateAsociar(Request $request) {
      $input = $request->all();
      if($input['apoderado_representante'] == 0) {
        $rules = array(
            'descripcion'         => $this->required50(),
            'adjuntar_poder'      => $this->requiredFile(),
            'observaciones'       => $this->max255(),
        );
      } else {
        $rules = array(
            'descripcion'         => $this->required50(),
            'adjuntar_poder'      => $this->requiredFile(),
            'fecha_fin_poder'     => $this->required255(),
            'observaciones'       => $this->max255(),
        );
      }

      $validator = Validator::make($input, $rules, $this->validationErrorMessages());
      $errores = array();
        $errores = array_merge($errores, $validator->getMessageBag()->toArray());

      if(!isset($input['checklist_items']) ||
        (isset($input['checklist_items']) && (sizeof($input['checklist_items']) != sizeof(trans('contratos.asociar_checklist'))))) {
        $errores["checklist_items"] = trans('contratos.error.checklist_items_no_chk');
      }

      if(!isset($input['terminos_y_condiciones'])) {
        $errores["terminos_y_condiciones"] = trans('contratos.error.terminos_y_condiciones');
      }

      if(isset($input['fecha_fin_poder']) && $this->fechaDeA($input['fecha_fin_poder'], 'd/m/Y', 'Y-m-d') < date('Y-m-d') == true) {
        $errores["fecha_fin_poder"] = trans('contratos.error.fecha_fin_poder_menor_hoy');
      }

      if(!$request->hasFile('adjuntar_poder')) {
        if(isset($input['adjuntar_poder'])) {
          $errores["adjuntar_poder"] = 'El máximo permitido es ' . ini_get('post_max_size');
        } else {
          if($input['apoderado_representante'] == 0)
            $errores["adjuntar_poder"] = trans('contratos.error.adjuntar_poder');
          else
            $errores["adjuntar_poder"] = trans('contratos.error.adjuntar_acta_estatuto');
        }
      }

      if ($validator->fails() || sizeof($errores) > 0) {
        $errores = array_merge($errores, $validator->getMessageBag()->toArray());
        $jsonResponse['status'] = false;
        $jsonResponse['errores'] = $errores;
        $jsonResponse['message'] = [];
        return response()->json($jsonResponse);
      }

      $user_publico = Auth::user()->user_publico;

      // Solo redeterminan automaticamente los bancos
      $redeterminacion_auto = false;
      $contrato = Contrato::find($input['contrato_id']);
      if($contrato->es_banco)
        $redeterminacion_auto = true;

      // Si tiene otro le pido confirmacion
      $solicitudes_contrato_old = SolicitudContrato::whereUserContratistaId(Auth::user()->user_publico->id)
                                                   ->whereContratoId($input['contrato_id'])->get();
      if($input['confirmado'] == 0) {
        foreach ($solicitudes_contrato_old as $key => $solicitudes_contrato_old) {

          if($solicitudes_contrato_old != null) {
            if($solicitudes_contrato_old->instancia_actual->esta_pendiente) {
              $jsonResponse['status'] = true;
              $jsonResponse['a_confirmar'] = true;
              $jsonResponse['conf_message'] = trans('contratos.confirmacion.esta_pendiente');
              return response()->json($jsonResponse);
            }

            if($solicitudes_contrato_old->instancia_actual->esta_aprobada) {
              $jsonResponse['status'] = true;
              $jsonResponse['a_confirmar'] = true;
              $jsonResponse['conf_message'] = trans('contratos.confirmacion.esta_aprobada');
              return response()->json($jsonResponse);
            }
          }
        }
      }

      try {
        $solicitudes_contrato[] = SolicitudContrato::create([
                                    'contrato_id'           => $input['contrato_id'],
                                    'descripcion'           => $input['descripcion'],
                                    'fecha_fin_poder'       => $input['fecha_fin_poder'],
                                    'user_contratista_id'   => $user_publico->id,
                                    'redeterminacion_auto'  => $redeterminacion_auto,
                                    'representante'         => $input['apoderado_representante'],
                                    'user_modifier_id'      => Auth::user()->id
                              ]);
      } catch (QueryException $e) {
        Log::error('QueryException', ['Exception' => $e]);
        $jsonResponse['status'] = false;
        $jsonResponse['errores'] = [trans('mensajes.error.insert_db')];
        $jsonResponse['message'] = [trans('mensajes.error.insert_db')];
        return response()->json($jsonResponse);
      }

      $poderes = $this->uploadPoderes($request);

      foreach($solicitudes_contrato as $keySolicitud => $valueSolicitud) {
        if(sizeof($poderes) > 0) {
          foreach($poderes as $keyPoder => $valuePoder) {
            $valueSolicitud->poderes()->attach($valuePoder->id);
          }
        }
        $valueSolicitud->observaciones = $input['observaciones'];
        $valueSolicitud->save();
      }

      $id_estado_pendiente = EstadoSolicitudContrato::whereNombre('contratos.estados.solicitud.pendiente')->first()->id;

      foreach($solicitudes_contrato as $keySolicitud => $valueSolicitud) {
        try {
          $instancia_solicitud = InstanciaSolicitudContrato::create([
                                      'solicitud_id'          => $valueSolicitud->id,
                                      'estado_id'             => $id_estado_pendiente,
                                      'user_id'               => Auth::user()->id
                                ]);
        } catch (QueryException $e) {
          Log::error('QueryException', ['Exception' => $e]);
          $jsonResponse['status'] = false;
          $jsonResponse['errores'] = [trans('mensajes.error.insert_db')];
          $jsonResponse['message'] = [trans('mensajes.error.insert_db')];
          return response()->json($jsonResponse);

        }
        $valueSolicitud->sendNuevaAsociacionNotification();
      }


      $jsonResponse['status'] = true;
      $jsonResponse['url'] = route('contrato.solicitudes');
      Session::flash('success', trans('mensajes.dato.solicitud_contrato').trans('mensajes.success.realizada'));
      return response()->json($jsonResponse);
    }

    /**
     * @param  int $id
    */
    public function verSolicitud($id) {
      $solicitud = SolicitudContrato::findOrFail($id);

      return view('contratos.solicitudes.show.index', compact('solicitud'));
    }

    /**
     * @param  \Illuminate\Http\Request  $request
    */
    public function exportarSolicitudes(Request $request) {
      $input = $request->all();
      $filtro = $input['excel_input'];

      $user_publico = Auth::user()->user_publico;
      $solicitudes_contrato = $user_publico->solicitudes_contrato;
      $solicitudes_contrato = $this->ordenarSolicitudes($solicitudes_contrato);

      $solicitudes_contrato = $solicitudes_contrato->map(function ($item, $key) {
          return [
              trans('forms.fecha_solicitud_th')   => $item->created_at,
              trans('forms.expedient_ppal_elec')  => $item->expedientes,
              trans('forms.descripcion')          => $item->descripcion,
              trans('forms.estado')               => $item->estado_nombre_color['nombre'],
              trans('forms.ultimo_movimiento_th') => $item->ultimo_movimiento,
          ];
      });

      return $this->toExcel(trans('index.mis') . trans('index.solicitudes_asociacion'),
                            $this->filtrarExportacion($solicitudes_contrato, $filtro));
    }

    /**
     * @param  \Illuminate\Http\Request  $request
    */
    private function uploadPoderes($request) {
      if(!$request->hasFile('adjuntar_poder'))
        return array();
      $input = $request->all();
      $user_id = Auth::user()->id;
      $files = $request->file('adjuntar_poder');
      foreach ($files as $keyFile => $file) {
        $extension = $file->getClientOriginalExtension();

        $nombre_original = basename($file->getClientOriginalName(), '.'.$file->getClientOriginalExtension());
        $nombre = $nombre_original . '.' . $extension;

        $filename = $user_id. '/'.$file->hashName();

        $arr_file = array('nombre' => $nombre , 'filename' => $filename );
        Storage::put('public/poderes/'.$user_id, $file);

        $poderes[] = Poder::create([
                      'adjunto'           => json_encode($arr_file),
                      'user_modifier_id'  => $user_id,
                      'fecha_fin_poder'   => $input['fecha_fin_poder'],
        ]);
      }
      return $poderes;
    }
    ////////////////// END Solicitudes //////////////////

    //////////////////  Salto //////////////////
    /**
     * @param  int $id_variacion
    */
    public function verSalto($id_variacion) {
      $salto = VariacionMesPolinomica::findOrFail($id_variacion);
      if(!Auth::user()->user_publico->tieneAsociadoElContrato($salto->obra->contrato->id))
          return redirect()->route('contrato.solicitudes');

      $calculador = new CalculoModelExtended;

      $user_contrato = UserContrato::whereUserContratistaId(Auth::user()->user_publico->id)
                                         ->whereContratoId($salto->obra->contrato->id)->first();

      return view('contratos.contratos.show.saltos.index', compact('salto', 'calculador', 'user_contrato'));
    }
    //////////////////  END Salto //////////////////
}
