<?php

namespace App\Http\Controllers\Redeterminaciones;

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

// use App\Events\NewSolicitudRedeterminacion;
use App\Events\PasosSolicitudRedeterminacion;

use Contrato\Contrato;
use SolicitudContrato\UserContrato;
use SolicitudRedeterminacion\SolicitudRedeterminacion;

use SolicitudRedeterminacion\Instancia\Instancia;
use SolicitudRedeterminacion\Instancia\TipoInstanciaRedet;

use SolicitudRedeterminacion\Instancia\Iniciada;
use SolicitudRedeterminacion\Instancia\AprobacionCertificados;
use SolicitudRedeterminacion\Instancia\VerificacionDesvio;
use SolicitudRedeterminacion\Instancia\CalculoPreciosRedeterminados;
use SolicitudRedeterminacion\Instancia\GeneracionExpediente;
use SolicitudRedeterminacion\Instancia\AsignacionPartidaPresupuestaria;
use SolicitudRedeterminacion\Instancia\ProyectoActaRDP;
use SolicitudRedeterminacion\Instancia\FirmaResolucion;
use SolicitudRedeterminacion\Instancia\EmisionCertificadoRDP;

use SolicitudRedeterminacion\SolicitudRDP\TipoSolicitudRDP;

use YacyretaPackageController\Redeterminaciones\SolicitudesRedeterminacionController as PackageSRController;
class SolicitudesRedeterminacionController extends PackageSRController {
    public function __construct() {
      View::share('ayuda', 'redeterminacion');
      $this->middleware('auth', ['except' => 'logout']);
    }

    public function index(Request $request) {
      $input = $request->all();
      $search_input = '';
      if(isset($input['search_input']))
        $search_input = $this->minusculaSinAcentos($input['search_input']);

      $solicitudes = Auth::user()->user_publico->solicitudes_de_mis_contratos;

      $solicitudes = $this->ordenar($solicitudes);

      if($request->getMethod() == "GET") {
        if($search_input != '') {
          $solicitudes = $this->filtrar($solicitudes, $search_input);
        }
        $solicitudes = $this->paginateCustom($solicitudes);
      } else {
        $solicitudes = $this->filtrar($solicitudes, $search_input);
        $solicitudes = $this->paginateCustom($solicitudes, 1);
      }

      return view('redeterminaciones.solicitudes.index', compact('solicitudes', 'search_input'));
    }

    /**
     * @param  SolicitudRedeterminacion\SolicitudRedeterminacion $solicitudes
    */
    private function ordenar($solicitudes) {
      $solicitudes = $solicitudes->groupBy(function($solicitud, $key) {
        return $solicitud->contrato->expediente_madre;
      });

      $toArray = array();
      foreach ($solicitudes as $keySolContrato => $valueSolContrato) {
        $toArray[$keySolContrato] = $valueSolContrato->sortBy(function($solicitud, $key) {
          return $solicitud->salto->nro_salto;
        }, 1, true)->all();
      }
      ksort($toArray);

      $ordered = collect();
      foreach ($toArray as $keyArray => $valueArray) {
        foreach ($valueArray as $key => $value) {
          $ordered->push($value);
        }
      }
      return $ordered;
    }

    /**
     * @param  Illuminate\Database\Eloquent\Collection $contratos_admin
     * @param  string $input_lower
    */
    private function filtrar($solicitudes, $input_lower) {
      if($input_lower == '')
        return $solicitudes;

      return $solicitudes->filter(function($solicitud) use($input_lower) {
        if($solicitud->a_termino)
          $a_termino = trans('sol_redeterminaciones.a_termino');
        else
          $a_termino = trans('sol_redeterminaciones.no_a_termino');
        return
          substr_count($this->minusculaSinAcentos($a_termino), $input_lower) > 0 ||
          substr_count($this->minusculaSinAcentos($solicitud->user_contrato->descripcion), $input_lower) > 0 ||
          substr_count($this->minusculaSinAcentos($solicitud->created_at), $input_lower) > 0 ||
          substr_count($this->minusculaSinAcentos($solicitud->expediente), $input_lower) > 0 ||
          substr_count($this->minusculaSinAcentos($solicitud->salto->contrato_moneda->contrato->expediente_madre), $input_lower) > 0 ;
      });

    }

    /**
     * @param  int $id nullabe, si es null deja elegir el contrato, sino hay un select con los propios
    */
    public function solicitar($id = null) {
      if($id != null) {
        $user_contrato = UserContrato::find($id);
        if(!Auth::user()->puedeSolicitarRedeterminacion($user_contrato))
          abort(403);
        $contrato = $user_contrato->contrato;
      }

      $traduccion_ddjj = 'contratos.solicitar_redeterminacion';

      return view('redeterminaciones.solicitudes.solicitar', compact('user_contrato', 'contrato', 'traduccion_ddjj'));
    }

    /**
     * @param  \Illuminate\Http\Request  $request
    */
    public function updateSolicitar(Request $request) {
      $input = $request->all();

      $contrato = Contrato::find($input['contrato_id']);
      $id = $contrato->contrato_original_sin_adendas->id;
      $user_contrato = UserContrato::whereContratoId($id)
                                     ->whereUserContratistaId(Auth::user()->user_publico->id)->first();

      if(!Auth::user()->puedeSolicitarRedeterminacion($user_contrato, $contrato)) {
        $jsonResponse['status'] = false;
        Session::flash('error', trans('mensajes.error.contrato_no_redeterminable'));
        $jsonResponse['message'] = trans('mensajes.error.contrato_no_redeterminable');
        return response()->json($jsonResponse);
      } else {
        foreach($contrato->contratos_monedas as $keyContratoMoneda => $valueContratoMoneda) {
          foreach($valueContratoMoneda->saltos_redeterminables as $keySalto => $valueSalto) {
            $publicacion = $valueSalto->publicacion;

            $a_termino = true;

            $fecha_publicacion = date_create($this->fechaDeA($publicacion->fecha_publicacion, 'd/m/Y', 'Y-m-d'));
            $hoy = date_create(date('Y-m-d'));

            if($publicacion->fecha_publicacion != trans('publicaciones.anterior_a_sistema') && $hoy->diff($fecha_publicacion)->days > 45) {
              $a_termino = false;
            }

            $solicitud_instancias = DB::transaction(function () use ($request, $input, $a_termino, $valueSalto, $valueContratoMoneda) {
              $solicitud = SolicitudRedeterminacion::create(['contrato_id'             => $input['contrato_id'],
                                                             'salto_id'                => $valueSalto->id,
                                                             'user_contratista_id'     => Auth::user()->user_publico->id,
                                                             'user_modifier_id'        => Auth::user()->id,
                                                             'a_termino'               => $a_termino,
                                                           ]);

              if(isset($input['observaciones'])) {
                $solicitud->observaciones = $input['observaciones'];
              }

              if($request->hasFile('adjunto')) {
                $solicitud->adjunto =  $this->uploadFile($request, $solicitud->id, 'adjunto', 'redeterminacion');
              }
              $solicitud->save();

              if($valueSalto->cuadro_comparativo != null) {
                $cuadro_comparativo = $valueSalto->cuadro_comparativo;
                $cuadro_comparativo->solicitud_id = $solicitud->id;
                $cuadro_comparativo->save();
              }
      ///////////// Creacion de Instancias /////////////
              if(!$valueContratoMoneda->moneda->lleva_analisis) {
                $tipos_instancia = TipoInstanciaRedet::where('cambia_estado', '=', 1)->get()->filter(function($ti) {
                  return $ti->otras_monedas;
                });
              } else {
                $tipos_instancia = TipoInstanciaRedet::where('cambia_estado', '=', 1)->orderBy('orden')->get();
              }

              $orden = 0;
              $primera = true;
              foreach ($tipos_instancia as $keySeccion => $valueTipoInstancia) {
                if($valueTipoInstancia->obligatoria || $valueTipoInstancia->debeRealizar($valueSalto)) {
                  $orden++;
                  $instancia = Instancia::create(['solicitud_id'          => $solicitud->id,
                                                  'tipo_instancia_id'     => $valueTipoInstancia->id,
                                                  'orden'                 => $orden
                                                ]);

                  // La primera despues de la iniciada es la actual
                  if($primera && $valueTipoInstancia->modelo != 'Iniciada') {
                    $instancia_actual_id = $instancia->id;
                    $instancia->fecha_inicio = date("Y-m-d H:i:s");
                    $instancia->save();
                    $primera = false;
                  }

                  $modelo = "SolicitudRedeterminacion\Instancia\\$valueTipoInstancia->modelo";
                  $instancia_model = $modelo::create(['instancia_id'      => $instancia->id,
                                                      'user_creator_id'   => Auth::user()->id
                                                    ]);
                }

                if($valueTipoInstancia->modelo == 'ProyectoActaRDP') {
                  $instancia_model->acta = $instancia_model->acta_content;
                  $instancia_model->resolucion = $instancia_model->resolucion_content;
                  $instancia_model->informe = $instancia_model->informe_content;

                  $instancia_model->save();
                }
              }

              $solicitud->instancia_actual_id = $instancia_actual_id;
              $solicitud->ultimo_movimiento =  date("Y-m-d H:i:s");
              $solicitud->save();
      ///////////// FIN Creacion de Instancias /////////////

              $valueSalto->solicitado = 1;
              $valueSalto->save();
            });
            // END Transaction

            // saltear aprobacion
            $solicitud = $valueSalto->solicitud;
            $instancia_actual = $solicitud->instancia_actual;
            $instancia_tipo = $instancia_actual->tipo_instancia;
            $instancia = $instancia_actual->instancia;

            if($instancia_tipo->modelo == 'AprobacionCertificados') {
              $certificados_aprobados = $valueSalto->tiene_certificado;
              if($certificados_aprobados === null) {
                $instancia->certificados_aprobados = $certificados_aprobados;
                $instancia->save();

                $solicitud->instancia_actual_id = $instancia_actual->instancia_siguiente->id;
                $solicitud->save();
              } elseif($certificados_aprobados) {

                $instancia->certificado_id = $valueSalto->certificado_salto->id;

                $instancia->certificados_aprobados = 1;
                $instancia->save();

                $solicitud->instancia_actual_id = $instancia_actual->instancia_siguiente->id;
                $solicitud->save();
              }
            }

            // event(new NewSolicitudRedeterminacion($solicitud));

            $tipo_instancia = TipoInstanciaRedet::whereModelo('Iniciada')->first();
            event(new PasosSolicitudRedeterminacion($solicitud, $tipo_instancia, "false"));

          }
          $valueContratoMoneda->save();
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
        Session::flash('success', trans('sol_redeterminaciones.solicitada'));
        $jsonResponse['message'] = trans('sol_redeterminaciones.solicitada');
        return response()->json($jsonResponse);
      }
    }

    public function ver($id) {
      $solicitudes_user = Auth::user()->user_publico->solicitudes_de_mis_contratos;

      $solicitud = SolicitudRedeterminacion::findOrFail($id);
      if(!$solicitudes_user->contains($solicitud)) {
        return redirect()->route('redeterminaciones.index');
      }

      $contrato = Contrato::find($solicitud->salto->contrato_moneda->contrato->id);
      $id = $contrato->contrato_original_sin_adendas->id;
      $user_contrato = UserContrato::whereContratoId($id)
                                   ->whereUserContratistaId(Auth::user()->user_publico->id)->first();

      if($user_contrato == null) {
        Log::error(trans('mensajes.error.contrato_no_asociado'), ['Usuario' => Auth::user()->id]);

        Session::flash('error', trans('mensajes.error.contrato_no_asociado'));
        return redirect()->route('contratos.index')
                         ->with(['error' => trans('mensajes.error.contrato_no_asociado')]);
      }

      return view('redeterminaciones.solicitudes.show.index', compact('solicitud', 'contrato'));
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

      $jsonResponse = $this->$instancia($request, $instancia, $id_solicitud, $correccion);
      $jsonResponse = $jsonResponse->getData();
      if($jsonResponse->status == true) {

        // Lo busco de nuevo porque algunas relaciones persisten
        $redeterminacion = SolicitudRedeterminacion::find($id_solicitud);

        $jsonResponse->historial_refresh = View::make('redeterminaciones.solicitudes.show.historial',
                                                       compact('redeterminacion'))->render();

        $jsonResponse->estado_contrato = View::make('redeterminaciones.solicitudes.show.estado_contrato',
                                                     compact('redeterminacion'))->render();

        $jsonResponse->acciones = View::make('redeterminaciones.solicitudes.show.acciones',
                                              compact('redeterminacion'))->render();

        $jsonResponse->datos_cargados = View::make('redeterminaciones.solicitudes.show.datos_cargados',
                                                    compact('redeterminacion'))->render();
      }
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
              'solicitud_id'          => $id_solicitud,
              'tipo_instancia_id'     => $valueTipoInstancia->id,
          ]);
          if($valueTipoInstancia->modelo == 'Iniciada') {
            $instancia_actual_id = $instancia->id;
          }
          //
          // if($valueTipoInstancia->modelo == 'CalculoPreciosRedeterminados') {
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
        $tipo_instancia = TipoInstanciaRedet::whereModelo($modelo)->first();
        $tipo_instancia_id = $tipo_instancia->id;
        if($tipo_instancia->cambia_estado) {
          $instancia = Instancia::whereTipoInstanciaId($tipo_instancia_id)
                                      ->whereSolicitudId($id_solicitud)->first();
          $instancia_model = $instancia->instancia;
        } else {
          try {
            $solicitud = SolicitudRedeterminacion::find($id_solicitud);
            $instancia_actual = $solicitud->instancia_actual;
            $orden_nueva = $instancia_actual->orden;

            $solicitud->updateOrdenInstanciasRestantes();

            $instancia = Instancia::create([
                'solicitud_id'          => $id_solicitud,
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
      $solicitudes = $user_publico->solicitudes_de_mis_contratos;
      $solicitudes = $this->ordenar($solicitudes);

      $solicitudes = $solicitudes->map(function ($solicitud, $key) {
          $arr = array();
          if($solicitud->a_termino)
            $arr[trans('sol_redeterminaciones.a_termino')] = strtoupper(trans('index.si'));
          else
            $arr[trans('sol_redeterminaciones.a_termino')] = strtoupper(trans('index.no'));

          $arr[trans('forms.expedient_ppal_elec')] = $solicitud->contrato->expediente_madre;
          $arr[trans('forms.description')] = $solicitud->user_contrato->descripcion;
          $arr[trans('forms.obra')] = $solicitud->descripcion;
          $arr[trans('forms.fecha_solicitud_th')] = $solicitud->created_at;
          $arr[trans('forms.expediente_solicitud_th')] = $solicitud->expediente;
          $arr[trans('forms.salto')] = $solicitud->salto->publicacion->mes_anio;

          if($solicitud->en_curso)
            $arr[trans('forms.estado')] = trans('index.esperando') . ' ' . $solicitud->estado_nombre_color['nombre'];
          else
            $arr[trans('forms.estado')] = $solicitud->estado_nombre_color['nombre'];

          $arr[trans('forms.ultimo_movimiento_th')] = $solicitud->created_at;

          return $arr;
      });

      return $this->toExcel(trans('index.mis') . trans('index.solicitudes_redeterminacion'),
                            $this->filtrarExportacion($solicitudes, $filtro));
    }
}
