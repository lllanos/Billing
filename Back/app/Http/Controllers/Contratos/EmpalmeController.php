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

use Contrato\Contrato;
use Contrato\ContratoMoneda\ContratoMoneda;
use Contrato\Certificado\Certificado;
use Contrato\Certificado\CertificadoMonedaContratista;
use Contrato\Certificado\ItemCertificado;

use CalculoRedeterminacion\VariacionMesPolinomica;

use Cronograma\ItemCronograma;
use Itemizado\Item;

use App\Http\Controllers\Contratos\ContratosControllerExtended;
class EmpalmeController extends ContratosControllerExtended {

    public function __construct() {
      View::share('ayuda', 'contrato');
      $this->middleware('auth', ['except' => 'logout']);
    }
//////////// PreConfirm Finalizar Empalme ////////////
   /**
    * @param int $id
    */
    public function preValidacion($id) {
      $contrato = Contrato::find($id);

      $errores = array();
      foreach ($contrato->redeterminaciones_por_moneda(true) as $keyRedet => $valueRedet) {
        $ultima_redet = $valueRedet['redeterminaciones']->sortByDesc('nro_salto')->first();
        $ultima_redet_fecha = $ultima_redet->publicacion->anio .'-' . $ultima_redet->publicacion->mes . '-01';

        if($ultima_redet->publicacion->mes < 10)
          $ultima_redet_fecha = $ultima_redet->publicacion->anio .'-' . '0' . $ultima_redet->publicacion->mes . '-01';

        $certificado = $contrato->certificados_empalme()
                                ->whereFecha($ultima_redet_fecha)
                                ->first();

        $fecha_redet = strtotime($ultima_redet_fecha);
        $fecha_redet = date('d/m/Y',$fecha_redet);

        if($fecha_redet > $contrato->fecha_acta_inicio) {
          if($certificado == null)
            $errores[$valueRedet['key']] = trans('redeterminaciones.mensajes.error.falta_certificado', ['moneda'  => $valueRedet['nombre'],
                                                                                                      'mes'     => $ultima_redet->publicacion->nombre_mes]);
        }

        if($ultima_redet->borrador)
          $errores[$ultima_redet->nro_salto] = trans('redeterminaciones.mensajes.error.redeterminacion_borrador', ['nro_salto'  => $ultima_redet->nro_salto]);
      }

      if((Auth::user()->cant('empalme-manage')) || count($errores) > 0) {
        $jsonResponse['status'] = false;
        $jsonResponse['title'] = trans('index.finalizar') . ' ' . trans('contratos.empalme');
        $jsonResponse['message'] = trans('redeterminaciones.mensajes.error.necesita_certificado'). '<ul>';
        foreach ($errores as $keyError => $valueError) {
          $jsonResponse['message'] .= '<li>' . $valueError . '</li>';
        }
        $jsonResponse['message'] .= '</ul>';
        $jsonResponse['error_container'] = '#errores-fin-empalme';
      } else {
        $jsonResponse['status'] = true;
      }

      return response()->json($jsonResponse);
    }
//////////// END PreConfirm Finalizar Empalme ////////////

//////////// Finalizar Empalme ////////////
    public function finalizarEmpalme($id) {
      if($this->preValidacion($id)->getData()->status != true) {
        $jsonResponse['status'] = false;
        $jsonResponse['message'] = [$this->preValidacion($id)->getData()->message];
        return response()->json($jsonResponse);
      }

    	$contrato = Contrato::find($id);

      // Debe tener permisos para guardar el empalme
      if((Auth::user()->cant('empalme-manage'))) {
          $jsonResponse['status'] = false;
          $jsonResponse['message'] = [trans('mensajes.error.permisos')];
          return response()->json($jsonResponse);
      }

      // El contrato debe tener el tilde de empalme
      // No puede tener un certificado en borrador
      // Debe existir al menos un certificado cargado o una redeterminación cargada
      if($contrato->has_certificados_empalme) {
        foreach ($contrato->certificados_empalme as $key => $certificado) {
          if($certificado->borrador) {
              $jsonResponse['status'] = false;
              $jsonResponse['message'] = [trans('mensajes.error.empalme_certificados_borrador')];
              return response()->json($jsonResponse);
          }
        }
      }

      if(!$contrato->empalme OR !$contrato->has_certificados_empalme) {
          $jsonResponse['status'] = false;
          $jsonResponse['message'] =  [trans('mensajes.error.empalme_sin_certificados')];
          return response()->json($jsonResponse);
      }

      $ultimas_redeterminaciones = collect();
      foreach ($contrato->redeterminaciones_por_moneda(true) as $keyRedet => $valueRedet) {
        // $ultima_redet = $valueRedet['redeterminaciones']->sortByDesc('nro_salto')->first();
        // Genero TODOS los saltos para poder visualizarlos
        $redeterminaciones = $valueRedet['redeterminaciones']->sortBy('nro_salto');
        foreach ($redeterminaciones as $keyRedeterminacion => $valueRedeterminacion) {
          $ultimas_redeterminaciones->push($valueRedeterminacion);
          $redeterminacionFecha = $valueRedeterminacion->publicacion->anio .'-' . $valueRedeterminacion->publicacion->mes . '-01';
          // $contrato_moneda = ContratoMoneda::find($valueRedeterminacion->contrato_moneda_id);
          $contrato_moneda = $valueRedeterminacion->contrato_moneda;
          $contrato_moneda->fecha_ultima_redeterminacion = $redeterminacionFecha;
          $contrato_moneda->save();
        }
      }

      // Las redeterminaciones se guardan entre los saltos marcadas como anteriores al sistema

      // Se actualiza el importe vigente del contrato. Se obtiene realizando la suma de:
      // Se actualiza el saldo del contrato (Cantidad/porcentaje sin certificar x nuevo precio)
      foreach ($contrato->contratos_monedas as $keyContratoMoneda => $valueContratoMoneda) {
        $contrato->reCalculoMontoYSaldo($valueContratoMoneda->id);
      }

      // Actualiza estado empalme
      try {
        $contrato->empalme_finalizado = 1;
        $contrato->save();
      }catch (QueryException $e) {
        Log::error('QueryException', ['Exception' => $e]);
          $jsonResponse['status'] = false;
          $jsonResponse['message'] = [trans('error.user.guardando_en_db')];
          return response()->json($jsonResponse);
      }

      // Genero saltos
      if(sizeof($ultimas_redeterminaciones) > 0) {
        foreach ($ultimas_redeterminaciones as $keyRedeterminacion => $valueRedeterminacion) {
          $variacion = VariacionMesPolinomica::create([
                  'polinomica_id'       => $valueRedeterminacion->contrato_moneda->polinomica_id,
                  'publicacion_id'      => $valueRedeterminacion->publicacion_id,
                  'contrato_moneda_id'  => $valueRedeterminacion->contrato_moneda_id,
                  'variacion'           => $valueRedeterminacion->variacion,
                  'es_salto'            => 1
                ]);

          $variacion->nro_salto = $valueRedeterminacion->nro_salto;
          $variacion->solicitado = 1;
          $variacion->calculado = 1;
          $variacion->empalme = 1;
          $variacion->save();

          $valueRedeterminacion->salto_id = $variacion->id;
          $valueRedeterminacion->save();
        }
      }

      $jsonResponse['status'] = true;
      $jsonResponse['refresh'] = true;
      Session::flash('success', trans('contratos.empalme') . trans('mensajes.success.finalizado'));
      $jsonResponse['message'] = [trans('contratos.empalme') . trans('mensajes.success.finalizado')];
      return response()->json($jsonResponse);
    }
//////////// END Finalizar Empalme ////////////
}
