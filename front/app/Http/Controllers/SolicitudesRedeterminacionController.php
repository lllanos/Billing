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

use App\Events\NewSolicitudRedeterminacion;
use App\Events\PasosSolicitudRedeterminacion;

use Contrato\Contrato;
use SolicitudContrato\UserContrato;
use SolicitudRedeterminacion\SolicitudRedeterminacion;

use SolicitudRedeterminacion\Instancia\Instancia;
use SolicitudRedeterminacion\Instancia\TipoInstancia;

use SolicitudRedeterminacion\Instancia\Iniciada;
use SolicitudRedeterminacion\Instancia\CargaPreciosRedeterminados;
use SolicitudRedeterminacion\Instancia\AsignacionPartidaPresupuestaria;
use SolicitudRedeterminacion\Instancia\ProyectoActaRDP;
use SolicitudRedeterminacion\Instancia\SolicitudRDP;
use SolicitudRedeterminacion\Instancia\FirmaContratista;
use SolicitudRedeterminacion\Instancia\EmisionDictamenJuridico;
use SolicitudRedeterminacion\Instancia\ActoAdministrativo;
use SolicitudRedeterminacion\Instancia\EmisionCertificadoRDP;
use SolicitudRedeterminacion\Instancia\ValidarPolizaCaucion;

use SolicitudRedeterminacion\Instancia\CargaPolizaCaucion;
use SolicitudRedeterminacion\Opciones\PolizaCaucion;

use YacyretaPackageController\SolicitudesRedeterminacionController as PackageRSController;
class SolicitudesRedeterminacionController extends PackageRSController {
    public function __construct() {
      View::share('ayuda', 'redeterminacion');
      $this->middleware('auth', ['except' => 'logout']);
    }

    public function index(Request $request) {
      $input = $request->all();
      $search_input = '';
      $redeterminaciones = Auth::user()->user_publico->solicitudes_de_mis_contratos;

      if($request->getMethod() != "GET") {
        $search_input = $input['search_input'];
        $input_lower = $this->minusculaSinAcentos($input['search_input']);

        if($input_lower != '') {
          $redeterminaciones = $redeterminaciones->filter(function($redeterminacion) use($input_lower) {
            if($redeterminacion->a_termino)
              $a_termino = trans('redeterminaciones.a_termino');
            else
              $a_termino = trans('redeterminaciones.no_a_termino');
            return
              substr_count($this->minusculaSinAcentos($a_termino), $input_lower) > 0 ||
              substr_count($this->minusculaSinAcentos($redeterminacion->user_contrato->descripcion), $input_lower) > 0 ||
              substr_count($this->minusculaSinAcentos($redeterminacion->obra->categoria_obra->nombr), $input_lower) > 0 ||
              substr_count($this->minusculaSinAcentos($redeterminacion->created_at), $input_lower) > 0 ||
              substr_count($this->minusculaSinAcentos($redeterminacion->expediente), $input_lower) > 0 ||
              substr_count($this->minusculaSinAcentos($redeterminacion->salto->obra->contrato->expedientes), $input_lower) > 0 ;
          });
        }
      }
      $redeterminaciones = $this->ordenar($redeterminaciones);
      $redeterminaciones = $this->paginateCustom($redeterminaciones);

      return view('redeterminaciones.index', compact('redeterminaciones', 'search_input'));
    }

    /**
     * @param  SolicitudRedeterminacion\SolicitudRedeterminacion $redeterminaciones
    */
    private function ordenar($redeterminaciones) {
      $redeterminaciones = $redeterminaciones->groupBy(function($redeterminacion, $key) {
        $um = $this->fechaDeA($redeterminacion->ultimo_movimiento, 'd/m/Y', 'm/d/Y');
        return strtotime($um);
      });

      $toArray = array();
      foreach ($redeterminaciones as $keyRed => $valueRed) {
        $toArray[$keyRed] = $valueRed->groupBy(function($redeterminacion, $key) {
          return $redeterminacion->contrato->expediente_ppal;
        });
      }
      krsort($toArray);

      $toArray2 = array();
      foreach ($toArray as $keyArray => $valueArray) {
        foreach ($valueArray as $key => $value) {
          $toArray2[$keyArray][$key] = $value->sortByDesc(function($redeterminacion, $key) {
            return $redeterminacion->salto->publicacion_id;
          })->all();
        }
      }

      $ordered = collect();
      foreach ($toArray as $keyArray => $valueArray) {
        foreach ($valueArray as $keyArray2 => $valueArray2) {
          foreach ($valueArray2 as $key => $value) {
            $ordered->push($value);
          }
        }
      }
      return $ordered;
    }

    /**
     * @param  int $id nullabe, si es null deja elegir el contrato, sino hay un select con los propios
    */
    public function solicitar($id = null) {
      if($id != null) {
        $user_contrato = UserContrato::find($id);
        if(!Auth::user()->puedeSolicitarRedeterminacion($user_contrato))
          abort(403);
      }

      $traduccion_ddjj = 'contratos.solicitar_redeterminacion';

      return view('redeterminaciones.solicitar', compact('user_contrato', 'traduccion_ddjj'));
    }

    /**
     * @param  \Illuminate\Http\Request  $request
    */
    public function updateSolicitar(Request $request) {
      $input = $request->all();

      $contrato = Contrato::find($input['contrato_id']);
      $redeterminacion = UserContrato::whereContratoId($input['contrato_id'])
                                   ->whereUserContratistaId(Auth::user()->user_publico->id)->first();

      if(!Auth::user()->puedeSolicitarRedeterminacion($redeterminacion)) {
        $jsonResponse['status'] = false;
        Session::flash('error', trans('mensajes.error.contrato_no_redeterminable'));
        $jsonResponse['message'] = trans('mensajes.error.contrato_no_redeterminable');
        return response()->json($jsonResponse);
      } else {

        foreach($redeterminacion->contrato->obras as $keyObra => $valueObra) {
          foreach($valueObra->saltos_redeterminables as $keySalto => $valueSalto) {
            $publicacion = $valueSalto->publicacion;

            $a_termino = true;

            $fecha_publicacion = date_create($this->fechaDeA($publicacion->created_at, 'd/m/Y', 'Y-m-d'));
            $hoy = date_create(date('Y-m-d'));

            if( $hoy->diff($fecha_publicacion)->days > 45) {
              $a_termino = false;
            }

            try {
              $solicitud = SolicitudRedeterminacion::create([
                'contrato_id'             => $input['contrato_id'],
                'salto_id'                => $valueSalto->id,
                'user_contratista_id'     => Auth::user()->user_publico->id,
                'user_modifier_id'        => Auth::user()->id,
                'a_termino'               => $a_termino,
              ]);

              if(isset($input['observaciones'])) {
                $solicitud->observaciones = $input['observaciones'];
              }

              if ($request->hasFile('adjunto')) {
                $solicitud->adjunto =  $this->uploadFile($request, $solicitud->id, 'adjunto');
              }
              $solicitud->save();


      ///////////// Creacion de Instancias /////////////
              if($redeterminacion->contrato->es_banco) {
                $tipos_instancia = TipoInstancia::where('cambia_estado', '=', 1)->get()->filter(function($ti) {
                  return $ti->banco;
                });
              } else {
                $tipos_instancia = TipoInstancia::where('cambia_estado', '=', 1)->orderBy('orden')->get();
              }
              $orden = 0;
              foreach ($tipos_instancia as $keySeccion => $valueTipoInstancia) {
                $orden++;
                try {
                  $instancia = Instancia::create([
                      'redeterminacion_id'    => $solicitud->id,
                      'tipo_instancia_id'     => $valueTipoInstancia->id,
                      'orden'                 => $orden
                  ]);
                  if($valueTipoInstancia->modelo == 'CargaPreciosRedeterminados') {
                    $instancia_actual_id = $instancia->id;
                    $instancia->fecha_inicio = date("Y-m-d H:i:s");
                    $instancia->save();
                  }

                  $modelo = "SolicitudRedeterminacion\Instancia\\$valueTipoInstancia->modelo";
                  $instancia_model = $modelo::create([
                      'instancia_id'      => $instancia->id,
                      'user_creator_id'   => Auth::user()->id
                  ]);

                } catch (QueryException $e) {
                  Log::error('QueryException', ['Exception' => $e]);
                  Session::flash('error', trans('mensajes.error.insert_db'));
                  $jsonResponse['status'] = false;
                  $jsonResponse['message'] = [trans('mensajes.error.insert_db')];
                  return response()->json($jsonResponse);
                }
              }

              $solicitud->instancia_actual_id = $instancia_actual_id;
              $solicitud->ultimo_movimiento =  date("Y-m-d H:i:s");
              $solicitud->save();
      ///////////// FIN Creacion de Instancias /////////////

              $valueSalto->solicitado = 1;
              $valueSalto->redeterminacion_id = $solicitud->id;
              $valueSalto->save();
            } catch (QueryException $e) {
              Log::error('QueryException', ['Exception' => $e]);
              $jsonResponse['status'] = false;
              $jsonResponse['errores'] = [trans('mensajes.error.insert_db')];
              $jsonResponse['message'] = [trans('mensajes.error.insert_db')];
              return response()->json($jsonResponse);
            }

            event(new NewSolicitudRedeterminacion($solicitud));

            $tipo_instancia = TipoInstancia::whereModelo('Iniciada')->first();
            event(new PasosSolicitudRedeterminacion($solicitud, $tipo_instancia, "false"));
          }
          $valueObra->save();

        }

        $contrato = Contrato::find($input['contrato_id']);
        try {
          $contrato->ultima_solicitud = date("Y-m-d H:i:s");
          $contrato->save();
        } catch (QueryException $e) {
          Log::error('QueryException', ['Exception' => $e]);
          $jsonResponse['status'] = false;
          $jsonResponse['errores'] = [trans('mensajes.error.insert_db')];
          $jsonResponse['message'] = [trans('mensajes.error.insert_db')];
          return response()->json($jsonResponse);
        }

        $jsonResponse['status'] = true;
        $jsonResponse['url'] = route('redeterminaciones.index');
        Session::flash('success', trans('redeterminaciones.solicitada'));
        $jsonResponse['message'] = trans('redeterminaciones.solicitada');
        return response()->json($jsonResponse);

      }
    }

    public function ver($id) {
      $redeterminaciones_user = Auth::user()->user_publico->solicitudes_de_mis_contratos;

      $redeterminacion = SolicitudRedeterminacion::findOrFail($id);
      if(!$redeterminaciones_user->contains($redeterminacion)) {
        return redirect()->route('redeterminaciones.index');
      }
      
      $user_contrato = UserContrato::whereContratoId($redeterminacion->salto->obra->contrato->id)
                                   ->whereUserContratistaId(Auth::user()->user_publico->id)->first();

      return view('redeterminaciones.show.index', compact('redeterminacion', 'user_contrato'));
    }

    // Retorna vista del modal de creacion
    public function createEdit($instancia, $id_solicitud, $correccion) {
      $redeterminacion = SolicitudRedeterminacion::find($id_solicitud);
      if(!$redeterminacion->puede_cargar_poliza && $instancia == 'CargaPolizaCaucion') {
        $jsonResponse['message'] = trans('redeterminaciones.error403');
        Log::error(trans('index.error403'), ['User' => $user, 'Instancia' => $instancia]);
        $jsonResponse['status'] = false;
        return response()->json($jsonResponse);
      }

      $redeterminacion = SolicitudRedeterminacion::find($id_solicitud);

      return view('redeterminaciones.show.create_update.'.$instancia, compact('instancia', 'id_solicitud', 'redeterminacion', 'correccion'));
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  stting  $instancia nombre del modelo
     * @param  int $id_solicitud
     * @param  bollean|string  $correccion
    */
    public function updateOrStore(Request $request, $instancia, $id_solicitud, $correccion) {
      $user = Auth::user();
      $redeterminacion = SolicitudRedeterminacion::find($id_solicitud);
      if(!$redeterminacion->puede_cargar_poliza && $instancia == 'CargaPolizaCaucion')
        return response()->json(['error' => 'Unauthenticated.'], 401);

      $jsonResponse = $this->$instancia($request, $instancia, $id_solicitud, $correccion);
      $jsonResponse = $jsonResponse->getData();
      if($jsonResponse->status == true) {


        // Lo busco de nuevo porque algunas relaciones persisten
        $redeterminacion = SolicitudRedeterminacion::find($id_solicitud);

        $jsonResponse->historial_refresh = View::make('redeterminaciones.show.historial',
                                                       compact('redeterminacion'))->render();

        $jsonResponse->estado_contrato = View::make('redeterminaciones.show.estado_contrato',
                                                     compact('redeterminacion'))->render();

        $jsonResponse->acciones = View::make('redeterminaciones.show.acciones',
                                              compact('redeterminacion'))->render();

        $jsonResponse->datos_cargados = View::make('redeterminaciones.show.datos_cargados',
                                                    compact('redeterminacion'))->render();
      }
      return response()->json($jsonResponse);
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  stting  $instancia nombre del modelo
     * @param  int $id_solicitud
     * @param  bollean|string  $correccion
    */
    public function CargaPolizaCaucion(Request $request, $instancia, $id_solicitud, $correccion) {
      $rules = array(
        'observaciones'           => $this->min3max1024(),
      );

      $input = $request->all();
      $validator = Validator::make(Input::all(), $rules, $this->validationErrorMessages());

        // Validaciones Custom
        $errores = array();

        // Es nueva
        if($input['new'] == 1) {
          if(!isset($input['adjuntar_poliza']))
            $errores['adjuntar_poliza'] = trans('mensajes.error.adjuntar_poliza');
          if(isset($input['adjuntar_poliza']) && !$request->hasFile('adjuntar_poliza'))
            $errores['adjuntar_poliza'] = $this->PostTooLargeMessage();
        }

        // Es nueva
        if(strpos($input['poliza'], '|||') != false) {
          if(!isset($input['adjuntar_poliza']))
            $errores['adjuntar_poliza'] = trans('mensajes.error.adjuntar_poliza');
        }

        if ($validator->fails() || sizeof($errores) > 0) {
          $errores = array_merge($errores, $validator->getMessageBag()->toArray());
          $jsonResponse['status'] = false;
          $jsonResponse['errores'] = $errores;
          $jsonResponse['message'] = [];
          return response()->json($jsonResponse);
        }

        $solicitud = SolicitudRedeterminacion::find($id_solicitud);
        if($input['new'] == 0) {
          $poliza = explode("|||", $input['poliza']);
          $id_poliza = $poliza[0];
        } else {
          $poliza_json = $this->uploadFile($request, $id_solicitud, 'adjuntar_poliza');
          $nueva_poliza = PolizaCaucion::create([
                            'descripcion' => $input['poliza'],
                            'adjunto'     => $poliza_json,
                            'contrato_id' => $solicitud->contrato->id
                          ]);
          $id_poliza = $nueva_poliza->id;
        }

        $instancia = $this->getInstancia($instancia, $id_solicitud, $correccion);
        $solicitud = SolicitudRedeterminacion::find($id_solicitud);

        $solicitud->ultimo_movimiento =  date("Y-m-d H:i:s");
        $instancia->observaciones = $input['observaciones'];

        $instancia->poliza_caucion_id = $id_poliza;
        $solicitud->poliza_caucion_id = $id_poliza;
        $instancia->user_creator_id = Auth::user()->id;

        try {
          $instancia->save();
          $solicitud->save();
        } catch (QueryException $e) {
          Log::error('QueryException', ['Exception' => $e]);
          Session::flash('error', trans('mensajes.error.insert_db'));
          $jsonResponse['status'] = false;
          $jsonResponse['message'] = [trans('mensajes.error.insert_db')];
          return response()->json($jsonResponse);
        }

        $jsonResponse['status'] = true;
        $jsonResponse['cambia_estado'] = $instancia->instancia->tipo_instancia->cambia_estado;
        $jsonResponse['message'] = [trans('redeterminaciones.exito.CargaPolizaCaucion')];
        return response()->json($jsonResponse);
    }

    /**
     * @param  stting  $modelo nombre del modelo
     * @param  int $id_solicitud
     * @param  bollean|string  $correccion
    */
    private function getInstancia($modelo, $id_solicitud, $correccion) {
      if($correccion == "true") {
        try {
          $instancia = Instancia::create([
              'redeterminacion_id'    => $id_solicitud,
              'tipo_instancia_id'     => $valueTipoInstancia->id,
          ]);
          if($valueTipoInstancia->modelo == 'Iniciada') {
            $instancia_actual_id = $instancia->id;
          }
          //
          // if($valueTipoInstancia->modelo == 'CargaPreciosRedeterminados') {
          //   $instancia->fecha_inicio = date("Y-m-d H:i:s");
          //   $instancia->save();
          // }

          $modelo = "SolicitudRedeterminacion\Instancia\\$valueTipoInstancia->modelo";
          $instancia_model = $modelo::create([
              'instancia_id'      => $instancia->id,
              'user_creator_id'   => Auth::user()->id
          ]);

        } catch (QueryException $e) {
          Log::error('QueryException', ['Exception' => $e]);
          Session::flash('error', trans('mensajes.error.insert_db'));
          $jsonResponse['status'] = false;
          $jsonResponse['message'] = trans('mensajes.error.insert_db');
          return response()->json($jsonResponse);
        }
        return $instancia_model;
      } else {
        $tipo_instancia = TipoInstancia::whereModelo($modelo)->first();
        $tipo_instancia_id = $tipo_instancia->id;
        if($tipo_instancia->cambia_estado) {
          $instancia = Instancia::whereTipoInstanciaId($tipo_instancia_id)
                                      ->whereRedeterminacionId($id_solicitud)->first();
          $instancia_model = $instancia->instancia;
        } else {
          try {
            $solicitud = SolicitudRedeterminacion::find($id_solicitud);
            $instancia_actual = $solicitud->instancia_actual;
            $orden_nueva = $instancia_actual->orden;

            $solicitud->updateOrdenInstanciasRestantes();

            $instancia = Instancia::create([
                'redeterminacion_id'    => $id_solicitud,
                'tipo_instancia_id'     => $tipo_instancia_id,
                'orden'                 => $orden_nueva
            ]);

            $modelo = "SolicitudRedeterminacion\Instancia\\$modelo";
            $instancia_model = $modelo::create([
                'instancia_id'      => $instancia->id,
                'user_creator_id'   => Auth::user()->id
            ]);

          } catch (QueryException $e) {
            Log::error('QueryException', ['Exception' => $e]);
            Session::flash('error', trans('mensajes.error.insert_db'));
            $jsonResponse['status'] = false;
            $jsonResponse['message'] = trans('mensajes.error.insert_db');
            return response()->json($jsonResponse);
          }

        }
        return $instancia_model;
      }
    }

    /**
     * @param  \Illuminate\Http\Request  $request
    */
    public function exportar(Request $request) {
      $input = $request->all();
      $filtro = $input['excel_input'];

      $user_publico = Auth::user()->user_publico;
      $redeterminaciones = $user_publico->solicitudes_de_mis_contratos;
      $redeterminaciones = $this->ordenar($redeterminaciones);

      $redeterminaciones = $redeterminaciones->map(function ($redeterminacion, $key) {
          $arr = array();
          if($redeterminacion->a_termino)
            $arr[trans('redeterminaciones.a_termino')] = strtoupper(trans('index.si'));
          else
            $arr[trans('redeterminaciones.a_termino')] = strtoupper(trans('index.no'));

          $arr[trans('forms.expedient_ppal_elec')] = $redeterminacion->contrato->expedientes;
          $arr[trans('forms.description')] = $redeterminacion->user_contrato->descripcion;
          $arr[trans('forms.obra')] = $redeterminacion->descripcion;
          $arr[trans('forms.obra')] =  $redeterminacion->obra->nombre . ' - ' . $redeterminacion->obra->categoria_obra->nombre;
          $arr[trans('forms.fecha_solicitud_th')] = $redeterminacion->created_at;
          $arr[trans('forms.expediente_solicitud_th')] = $redeterminacion->expediente;
          $arr[trans('forms.salto')] = $redeterminacion->salto->publicacion->mes_anio;

          if($redeterminacion->en_curso)
            $arr[trans('forms.estado')] = trans('index.esperando') . ' ' . $redeterminacion->estado_nombre_color['nombre'];
          else
            $arr[trans('forms.estado')] = $redeterminacion->estado_nombre_color['nombre'];

          $arr[trans('forms.ultimo_movimiento_th')] = $redeterminacion->created_at;

          return $arr;
      });

      return $this->toExcel(trans('index.mis') . trans('index.solicitudes_redeterminacion'),
                            $this->filtrarExportacion($redeterminaciones, $filtro));
    }

    public function uploadFile($request, $id_solicitud, $name) {
      $file = $request->file($name);

      $extension = $file->getClientOriginalExtension();

      $nombre_original = basename($file->getClientOriginalName(), '.'.$file->getClientOriginalExtension());
      $nombre = $nombre_original. '.' . $extension;

      $filename = $id_solicitud. '/'.$file->hashName();

      $arr_file = array('nombre' => $nombre , 'filename' => env('APP_URL') . '/storage/redeterminacion/' . $filename );
      Storage::put('public/redeterminacion/' . $id_solicitud, $file);

      return json_encode($arr_file);
    }
}
