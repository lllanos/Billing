<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use SolicitudRedeterminacion\SolicitudRedeterminacion;
use DateTime;
use Log;

class PasarInstancias implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $args;
    public $user_id;
    public $sub_queue;

    /**
     * Create a new job instance.
     * @return void
     * @param string $args json_encoded variables
    */
    public function __construct($args) {
      // Valor que guardo en db
      $this->args = $args;
      $this->sub_queue = 'calculos_variacion';

      $args = json_decode($args);
      if($args != null) {
        $this->user_id = $args->user_id;
      }
    }

    public function handle() {
      $solicitudes = SolicitudRedeterminacion::all()->filter(function($solicitud) {
                      return $solicitud->estado_nombre_color['nombre'] == trans('sol_redeterminaciones.instancias.Iniciada');
                     });

      foreach ($solicitudes as $keySolicitud => $valueSolicitud) {
        $valueSolicitud->pasarInstancia();
      }

      // Agrego pasar de Certificados si los tiene
      $solicitudes = SolicitudRedeterminacion::all()->filter(function($solicitud) {
                      return $solicitud->estado_nombre_color['nombre'] == trans('sol_redeterminaciones.instancias.AprobacionCertificados');
                     });

      foreach ($solicitudes as $keySolicitud => $valueSolicitud) {
        $salto = $valueSolicitud->salto;

        $instancia_actual = $valueSolicitud->instancia_actual;
        $instancia = $instancia_actual->instancia;

        $certificados_aprobados = $salto->tiene_certificado;

        if($certificados_aprobados === null) {
          $instancia->certificados_aprobados = $certificados_aprobados;
          $instancia->save();

          $valueSolicitud->instancia_actual_id = $instancia_actual->instancia_siguiente->id;
          $valueSolicitud->save();
        } elseif($certificados_aprobados) {

          $instancia->certificado_id = $salto->certificado_salto->id;

          $instancia->certificados_aprobados = 1;
          $instancia->save();

          $valueSolicitud->instancia_actual_id = $instancia_actual->instancia_siguiente->id;
          $valueSolicitud->save();
        }
      }
    }

  }
