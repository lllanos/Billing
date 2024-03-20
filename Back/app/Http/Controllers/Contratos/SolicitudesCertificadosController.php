<?php

namespace App\Http\Controllers\Contratos;

use Illuminate\Database\QueryException;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

use DB;
use DateTime;
use Log;
use Response;
use Storage;
use View;
use Dompdf\Dompdf;

use App\Jobs\InstanciaCalculoPrecios;

use Contrato\Certificado\Certificado;
use Contrato\Certificado\CertificadoRedeterminado;
use Contrato\Certificado\ItemCertificado;

use SolicitudRedeterminacion\SolicitudRedeterminacion;
use SolicitudRedeterminacion\Instancia\Instancia;
use SolicitudRedeterminacion\Instancia\VerificacionDesvio;
use SolicitudRedeterminacion\Instancia\TipoInstanciaRedet;

use YacyretaPackageController\Contratos\SolicitudesCertificadosController as PackageSCSController;
class SolicitudesCertificadosController extends PackageSCSController {

    public function __construct() {
      View::share('ayuda', 'contrato');
      $this->middleware('auth', ['except' => 'logout']);
    }

    // visualizar bandeja solicitudes certificaciones
    /**
     * @param  \Illuminate\Http\Request  $request
    */
    public function indexEnProceso(Request $request) {
      $input = $request->all();
      $search_input = '';
      if(isset($input['search_input']))
        $search_input = $this->minusculaSinAcentos($input['search_input']);

      $certificados = Certificado::whereEmpalme(0)->whereBorrador(1)->get();
      $user = Auth::user();
      $estados =['a_validar', 'emitido'];
      if($user->usuario_causante) {
        $certificados = Certificado::whereEmpalme(0)
                                   ->with('last_instancia.estado')->get();

        $certificados = $certificados->filter(function($certificado) use ($user, $estados) {
                                              return $user->puedeVerContrato($certificado->contrato)
                                                     && in_array($certificado->last_instancia->estado->nombre, $estados);
                                          });
      } else {
        $certificados = Certificado::whereEmpalme(0)
                                   ->with('last_instancia.estado')->get();

        $certificados = $certificados->filter(function($certificado) use ($user, $estados) {
                                              return in_array($certificado->last_instancia->estado->nombre, $estados);
                                          });
      }

      if($request->getMethod() == "GET") {
        if($search_input != '') {
          $certificados = $this->filtrar($certificados, $search_input);
        }
        $certificados = $this->paginateCustom($certificados);
      } else {
        $certificados = $this->filtrar($certificados, $search_input);
        $certificados = $this->paginateCustom($certificados, 1);
      }

      $finalizadas = false;
      return view('contratos.certificados.solicitudes.index', compact('certificados', 'search_input', 'finalizadas'));
    }

    /**
     * @param  \Illuminate\Http\Request  $request
    */
    public function indexFinalizadas(Request $request) {
      $input = $request->all();
      $search_input = '';
      if(isset($input['search_input']))
        $search_input = $this->minusculaSinAcentos($input['search_input']);

      $user = Auth::user();
      $estados =['a_corregir', 'aprobado'];
      if($user->usuario_causante) {
        $certificados = Certificado::whereEmpalme(0)
                                   ->with('last_instancia.estado')->get();

        $certificados = $certificados->filter(function($certificado) use ($user, $estados) {
                                           return $user->puedeVerContrato($certificado->contrato)
                                           && in_array($certificado->last_instancia->estado->nombre, $estados);
                                         });
      } else {
        $certificados = Certificado::whereEmpalme(0)
                                   ->with('last_instancia.estado')->get();

        $certificados = $certificados->filter(function($certificado) use ($user, $estados) {
                                           return in_array($certificado->last_instancia->estado->nombre, $estados);
                                         });
      }

      if($request->getMethod() == "GET") {
        if($search_input != '') {
          $certificados = $this->filtrar($certificados, $search_input);
        }
        $certificados = $this->paginateCustom($certificados);
      } else {
        $certificados = $this->filtrar($certificados, $search_input);
        $certificados = $this->paginateCustom($certificados, 1);
      }

      $finalizadas = true;
      return view('contratos.certificados.solicitudes.index', compact('certificados', 'search_input', 'finalizadas'));
    }

    /**
     * @param  string $search_input
    */
    private function filtrar($certificados, $search_input) {
      if($search_input == '')
        return $certificados;

      return $certificados->filter(function($certificado) use($search_input) {
        if($certificado->redeterminado)
          $tipo = trans('certificado.redeterminado');
        else
          $tipo = trans('certificado.basico');
        return
          $this->stringContains($certificado->created_at, $search_input) ||
          $this->stringContains($certificado->contrato->expediente_madre, $search_input) ||
          $this->stringContains(trans('index.mes') . $certificado->mes . ' - ' . $certificado->mesAnio('fecha', 'Y-m-d'), $search_input) ||
          $this->stringContains($tipo, $search_input) ||
          $this->stringContains($certificado->estado['nombre_trans'], $search_input);
      });
    }

    /**
    * @param  int $id
    */
    public function aprobarCertificado($id) {
      $user = Auth::user();
      $certificado = Certificado::find($id);

      if($user->cant('certificado-aprobar')) {
        Log::error(trans('index.error403'), ['User' => $user, 'Intenta' => 'certificado-aprobar']);
        $jsonResponse['message'] = [trans('index.error403')];
        $jsonResponse['permisos'] = true;
        $jsonResponse['status'] = false;
        return response()->json($jsonResponse);
      }

      if(!$certificado->borrador) {
        Session::flash('error', trans('certificado.sin_permisos'));
        return redirect()->route('contratos.index')
                         ->with(['error' => trans('certificado.sin_permisos')]);
      }

      $certificado->borrador = 0;
      $certificado->save();

      $certificado->createInstancia('aprobado');
      $this->createInstanciaHistorial($certificado, 'certificado', 'aprobado');

      // aprobar instancia en solicitud de redeterminacion
      $solicitudes = $certificado->contrato->solicitudes_redeterminacion()
                                           ->whereFinalizada(0)
                                           ->where('monto_vigente', null)
                                           ->get();
      if(count($solicitudes)) {
        // checkeo si existe solicitud x mes actual
        $fecha_certificado = date('m/Y', strtotime($certificado->fecha));

        foreach ($solicitudes as $keySolicitud => $valueSolicitud) {
          $mes_publicacion = $valueSolicitud->salto->publicacion->mes_anio_anterior;
          if($fecha_certificado == $mes_publicacion) {
            // si la solicitud esta en esa instancia la apruebo
            $instancia_actual = $valueSolicitud->instancia_actual;
            $instancia_tipo = $instancia_actual->tipo_instancia;

            if($instancia_tipo->modelo == 'AprobacionCertificados') {
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

                //renumero el orden de las demas instancias
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

      $contrato = $certificado->contrato;
      // Creo los de empalme
      $contrato = $certificado->contrato;
      foreach ($contrato->redeterminaciones_empalme as $keyRedeterminacion => $valueRedeterminacion) {
        $solicitud = new SolicitudRedeterminacion();
        $solicitud->a_termino = true;
        app('SolicitudesRedeterminacionController')->createCertificadosRedeterminadosAnteriores($valueRedeterminacion, $certificado);
      }

      $solicitudes = $contrato->solicitudes_redeterminacion()
                              ->whereFinalizada(1)
                              ->get();

      // Creo los que tienen solicitud
      foreach ($solicitudes as $keySolicitud => $valueSolicitud) {
        $redeterminacion = $valueSolicitud->redeterminacion;
        app('SolicitudesRedeterminacionController')->createCertificadosRedeterminadosAnteriores($redeterminacion, $certificado);
      }

      foreach ($certificado->cert_moneda_contratista as $keyCertifMonedaContr => $valueCertifMonedaContr) {

        $contrato_moneda = $valueCertifMonedaContr->contrato_moneda;
        $certificado->contrato->reCalculoMontoYSaldo($contrato_moneda->id);
      }

      Session::flash('success', trans('mensajes.dato.certificado').trans('mensajes.success.aprobado'));
      $jsonResponse['status'] = true;
      $jsonResponse['refresh'] = false;
      $jsonResponse['url'] = route('contratos.ver', ['id' => $certificado->contrato_id]);
      $jsonResponse['message'] = trans('mensajes.dato.certificado').trans('mensajes.success.aprobado');
      return response()->json($jsonResponse);
    }

    /**
    * @param int $id
    */
    public function aprobarCertificadoRedeterminado($id) {
      $certificado = CertificadoRedeterminado::findOrFail($id);
      if(!$certificado->puede_aprobar_redeterminado) {
        $jsonResponse['status'] = false;
        Log::error(trans('mensajes.error.contrato_no_asociado'), ['Usuario' => Auth::user()->id]);
        $jsonResponse['message'] = [trans('mensajes.error.contrato_no_asociado')];
        return response()->json($jsonResponse);
      }

      $certificado->createInstancia('aprobado');

      foreach ($certificado->cert_moneda_contratista as $keyCertifMonedaContr => $valueCertifMonedaContr) {
        $contrato_moneda = $valueCertifMonedaContr->contrato_moneda;
        $certificado->contrato->reCalculoMontoYSaldo($contrato_moneda->id);
      }

      $jsonResponse['status'] = true;
      $jsonResponse['refresh'] = true;
      $jsonResponse['message'] = [trans('certificado.mensajes.aprobar')];

      return response()->json($jsonResponse);
    }

    /**
    * @param  int $id
    */
    public function rechazarCertificado(Request $request, $id) {
      $input = $request->except('_token');
      $user = Auth::user();

      if($user->cant('certificado-rechazar')) {
        Log::error(trans('index.error403'), ['User' => $user, 'Intenta' => 'certificado-rechazar']);
        $jsonResponse['message'] = [trans('index.error403')];
        $jsonResponse['permisos'] = true;
        $jsonResponse['status'] = false;
        return response()->json($jsonResponse);
      }

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

      $certificado = Certificado::find($id);

      if(!$certificado->borrador) {
        Session::flash('error', trans('certificado.sin_permisos'));
        return redirect()->route('contratos.index')
                         ->with(['error' => trans('certificado.sin_permisos')]);
      }
      $certificado->borrador = 1;
      $certificado->save();
      $motivo = $input['motivo_rechazo'];

      $certificado->createInstancia('a_corregir', $motivo);
      $this->createInstanciaHistorial($certificado, 'certificado', 'a_corregir', $motivo);

      Session::flash('success', trans('mensajes.dato.certificado').trans('mensajes.success.rechazado'));
      $jsonResponse['status'] = true;
      $jsonResponse['refresh'] = false;
      $jsonResponse['url'] = route('contratos.ver', ['id' => $certificado->contrato_id]);
      $jsonResponse['message'] = trans('mensajes.dato.certificado').trans('mensajes.success.rechazado');
      return response()->json($jsonResponse);
    }
}
