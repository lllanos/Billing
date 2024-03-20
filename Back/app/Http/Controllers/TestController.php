<?php

namespace App\Http\Controllers;

use Illuminate\Database\QueryException;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
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
use App\User;

use App\Jobs\CalculoVariacionEnPublicacion;
use App\Jobs\CalculoPrecios;
use App\Jobs\InstanciaCalculoPrecios;

use Yacyreta\Jobs\JobCustom;

use SolicitudContrato\UserContrato;
use Contrato\Contrato;
use Contrato\ContratoMoneda\ContratoMoneda;
use Contrato\TipoContrato;

use Contrato\Certificado\Certificado;
use Contrato\Certificado\ItemCertificado;
use SolicitudRedeterminacion\Instancia\Instancia;
use SolicitudRedeterminacion\Instancia\VerificacionDesvio;
use SolicitudRedeterminacion\Instancia\TipoInstanciaRedet;

use SolicitudContrato\Poder;

use YacyretaTraits\YacyretaTrait;

class TestController extends Controller {
    use RegistersUsers, YacyretaTrait;

    public function __construct() {
      View::share('ayuda', 'seguridad');
      $this->middleware('auth', ['except' => 'logout']);
    }

    public function calculosVariacion() {
      echo 'Lanzando "CalculoVariacionEnPublicacion" </br>';
      dispatch((new CalculoVariacionEnPublicacion())->onQueue('calculos_variacion'));

      echo 'Lanzando "InstanciaCalculoPrecios" </br>';
      dispatch((new InstanciaCalculoPrecios())->onQueue('calculos_variacion'));

      echo 'FIN';
    }

    public function runJobs() {
      $i = 0;
      $this->toLog('Inicio runJobs');

      dispatch((new CalculoVariacionEnPublicacion())->onQueue('calculos_variacion'));
      $this->toLog('CalculoVariacionEnPublicacion');

      dispatch((new CalculoPrecios())->onQueue('calculos_variacion'));
      $this->toLog('CalculoPrecios');

      dispatch((new InstanciaCalculoPrecios())->onQueue('calculos_variacion'));
      $this->toLog('InstanciaCalculoPrecios');

      foreach (JobCustom::all() as $keyJob => $valueJob) {
        if($valueJob->available_at != $valueJob->created_at) {
          $valueJob->available_at = $valueJob->created_at;
          $valueJob->save();
          $i++;
        }
      }

      echo 'Se forzó la ejecución de ' . $i . ' jobs';
      $this->toLog('FIN runJobs');
    }

    public function asociarAPublic() {
      $user = User::whereEmail('public@public.com')->whereUsuarioSistema(0)->first();
      if($user == null) {
        echo 'NO Existe el Usuario';
        die();
      }

      $user_publico = $user->user_publico;
      $ids_asociados = array();
      echo 'El Usuario tiene asociados los siguientes ' . count($user_publico->contratos) . ' contratos: </br>';
      foreach ($user_publico->contratos as $keyContrato => $valueContrato) {
        echo $valueContrato->contrato->expediente_madre . ', ';
        $ids_asociados[$valueContrato->contrato_id] = $valueContrato->contrato_id;
      }

      $tipo_adenda_ampliacion_id = TipoContrato::whereNombre('adenda_ampliacion')->first()->id;
      $all_contratos = Contrato::whereBorrador(0)->get();
      $poder = Poder::first();
      $cant = 0;
      foreach ($all_contratos as $keyContrato => $valueContrato) {
        if(!in_array($valueContrato->id, $ids_asociados) && $valueContrato->tipo_id != $tipo_adenda_ampliacion_id) {
          $user_contrato = UserContrato::create([
                                      'contrato_id'           => $valueContrato->id,
                                      'descripcion'           => 'Desde Proceso',
                                      'user_contratista_id'   => $user_publico->id,
                                      'fecha_fin_poder'       => null,
                                      'poder_id'              => $poder->id,
                                      'user_modifier_id'      => Auth::user()->id
                                      ]);
          echo 'Se asoció: ' . $valueContrato->expediente_madre . '</br>';
          $cant++;
        }
      }
      if($cant > 0)
      echo 'Se asociaron: ' . $cant . ' contratos';
    }

    public function asociarContratoIdAPublic($contrato_id) {
      $user = User::whereEmail('public@public.com')->whereUsuarioSistema(0)->first();
      if($user == null) {
        echo 'NO Existe el Usuario';
        die();
      }

      $user_publico = $user->user_publico;
      $ids_asociados = array();
      foreach ($user_publico->contratos as $keyContrato => $valueContrato) {
        $ids_asociados[$valueContrato->contrato_id] = $valueContrato->contrato_id;
      }

      $tipo_adenda_ampliacion_id = TipoContrato::whereNombre('adenda_ampliacion')->first()->id;
      $contrato = Contrato::find($contrato_id);
      if($contrato == null) {
        echo 'NO Existe el Contrato de <b>id=' . $contrato_id . '</b>';
      } elseif($contrato->tipo_id == $tipo_adenda_ampliacion_id) {
        echo 'El Contrato es Adenda de Ampliación';
      } elseif($contrato->borrador == 1) {
        echo 'El Contrato está en estado Borrador';

      } elseif(!in_array($contrato_id, $ids_asociados)) {
        $poder = Poder::first();
        $user_contrato = UserContrato::create([
                                    'contrato_id'           => $contrato_id,
                                    'descripcion'           => 'Desde Proceso',
                                    'user_contratista_id'   => $user_publico->id,
                                    'fecha_fin_poder'       => null,
                                    'poder_id'              => $poder->id,
                                    'user_modifier_id'      => Auth::user()->id
                                    ]);
        echo 'Se asoció el contrato ' . $valueContrato->expediente_madre . '</br>';
      } else {
        echo 'El usuario ya tenía asociado el contrato ' . $valueContrato->expediente_madre . '</br>';
      }
    }

    public function fixCertificadoCambioAnio() {
      return null;
      foreach (Certificado::whereEmpalme(0)->whereBorrador(0)->whereRedeterminado(0)->get() as $keyCert => $certificado) {

        $solicitudes = $certificado->contrato->solicitudes_redeterminacion()
                                             ->whereFinalizada(0)
                                             ->where('monto_vigente', null)
                                             ->get();
        if($solicitudes) {
          // checkeo si existe solicitud x mes actual
          $fecha_certificado = date('m/Y', strtotime($certificado->fecha));

          foreach ($solicitudes as $keySolicitud => $valueSolicitud) {
            $mes_publicacion = $valueSolicitud->salto->publicacion->mes_anio_anterior;

            if($fecha_certificado == $mes_publicacion ) {
              // si la solicitud esta en esa instancia la apruebo
              $instancia_actual = $valueSolicitud->instancia_actual;
              $instancia_tipo = $instancia_actual->tipo_instancia;

              if($instancia_tipo->modelo == 'AprobacionCertificados') {
                echo $certificado->fecha . ' --> '. $valueSolicitud->salto->publicacion->mes_anio . '</br>';
                $desvio = $valueSolicitud->salto->desvio_acumulado;
                $desvio = abs($desvio);

                if($desvio > ItemCertificado::getPorcentajeDesvio()) {
                  // creo instancia de desvio si aplica
                  $tipoInstancia = TipoInstanciaRedet::whereModelo('VerificacionDesvio')->first()->id;

                  $instanciaDesvio = Instancia::create(['solicitud_id'          => $valueSolicitud->id,
                                                        'tipo_instancia_id'     => $tipoInstancia,
                                                        'orden'                 => 99
                                                      ]);

                  $instancia_model = VerificacionDesvio::create(['instancia_id'             => $instanciaDesvio->id,
                                                                 'user_creator_id'          => Auth::user()->id
                                                               ]);

                  // renumero el orden de las demas instancias
                  $allInstancias = Instancia::whereSolicitudId($valueSolicitud->id)->orderBy('orden')->get();

                  foreach ($allInstancias as $keyInstancia => $valueInstancia) {
                    $valueInstancia->orden = $valueInstancia->tipo_instancia_id;
                    $valueInstancia->save();
                  }
                }

                $instancia = $instancia_actual->instancia;
                $instancia->certificado_id = $certificado->id;
                $instancia->certificados_aprobados = 1;
                $instancia->save();

                $instancia_siguiente = $instancia_actual->instancia_siguiente;
                $valueSolicitud->instancia_actual_id = $instancia_siguiente->id;
                $valueSolicitud->save();

                $instancia_siguiente->fecha_inicio = date("Y-m-d H:i:s");
                $instancia_siguiente->save();

                if($instancia_siguiente->tipo_instancia->modelo == 'CalculoPreciosRedeterminados') {
                  dispatch((new InstanciaCalculoPrecios($valueSolicitud->id))->onQueue('calculos_variacion'));
                }
              }
            }
          }
        }

      }
    }

    /**
     * @param  int   $id
    */
    public function reCalculoMontoYSaldo($contrato_id) {
      $contrato = Contrato::find($contrato_id);
      if($contrato == null) {
        echo 'NO Existe el Contrato de <b>id=' . $contrato_id . '</b>';
      }

      foreach (ContratoMoneda::whereClaseId($contrato_id)->get() as $keyContratoMoneda => $valueContratoMoneda) {
        if($valueContratoMoneda->contrato
            && !$valueContratoMoneda->contrato->borrador
            && $valueContratoMoneda->contrato->completo) {
              echo 'Cálculo de ContratoMoneda id=' . $valueContratoMoneda->id . ' (' . $valueContratoMoneda->moneda->nombre_simbolo . ') comenzado' . '<br>';
              echo '&ensp; - <b>Monto Vigente</b>: ' . $valueContratoMoneda->monto_vigente . '<br>';
              echo '&ensp; - <b>Saldo</b>: ' . $valueContratoMoneda->saldo . '<br>';

              $valueContratoMoneda->reCalculoMontoYSaldo();
              echo 'Cálculo de ContratoMoneda id=' . $valueContratoMoneda->id . ' (' . $valueContratoMoneda->moneda->nombre_simbolo . ') finalizado:' . '<br>';
              echo '&ensp; - <b>Nuevo Monto Vigente</b>: ' . $valueContratoMoneda->monto_vigente . '<br>';
              echo '&ensp; - <b>Nuevo Saldo</b>: ' . $valueContratoMoneda->saldo . '<br>';
        }
      }
    }
  }
