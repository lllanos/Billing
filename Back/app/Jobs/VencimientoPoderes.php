<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use SolicitudContrato\Poder;
use DateTime;
use Log;

use YacyretaJobs\BaseJob;
class VencimientoPoderes extends BaseJob implements ShouldQueue {
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
      $this->sub_queue = 'poderes';

      $args = json_decode($args);
      if($args != null) {
        $this->user_id = $args->user_id;
      }
    }

    public function handle() {
      $poderes = Poder::where('fecha_fin_poder', '!=', null)->get();
      $hoy = date('d/m/Y');
      $hoy = DateTime::createFromFormat('d/m/Y', $hoy);
      foreach ($poderes as $keyPoder => $valuePoder) {
        if($valuePoder->fecha_fin_poder != '') {
          $fecha_fin_poder = DateTime::createFromFormat('d/m/Y', $valuePoder->fecha_fin_poder);

          $this->toLog('Fin Poder: ' . $fecha_fin_poder->format('d/m/Y')
                       . ' Hoy: ' . $hoy->format('d/m/Y'));

          if($fecha_fin_poder <= $hoy) {
            foreach ($valuePoder->users_contrato as $keyUserContrato => $valueUserContrato) {
              $valueUserContrato->user_publico->user->sendVencimientoPoderNotification($valueUserContrato->contrato_id);
              $valueUserContrato->delete();
            }
            $valuePoder->delete();
          }
        }
      }
    }

    public function fechaDeA($fecha, $formato_original, $formato_nuevo) {
      return DateTime::createFromFormat($formato_original, $fecha)->format($formato_nuevo);
    }
  }
