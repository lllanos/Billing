<?php

namespace App\Listeners;

use App\Events\PasosSolicitudRedeterminacion;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use Log;

use App\Role;
use AlarmaSolicitud\AlarmaSolicitud;

class Paso {
    /**
     * Create the event listener.
     * @return void
     */
    public function __construct() {
        //
    }

    /**
     * Handle the event.
     * @param  PasosSolicitudRedeterminacion  $event
     * @return void
     */
    public function handle(PasosSolicitudRedeterminacion $event) {
      $solicitud = $event->solicitud;

      // $correccion = 0;
      // if($event->correccion == "true")
      //   $correccion = 1;

      $alarmas = AlarmaSolicitud::whereDesencadenanteId($event->desencadenante_id)
                                // ->whereCorreccion($correccion)
                                ->get()
                                ->filter(function($alarma) {
                                  return $alarma->habilitada;
                                });

      foreach ($alarmas as $keyAlarma => $valueAlarma) {
        if($valueAlarma->usuario_sistema == 1) {
    			$role = Role::find($valueAlarma->role_id);

          if($valueAlarma->responsable_contrato) {
            $causante_id = $solicitud->contrato->causante_id;
          } else {
            $causante_id = $valueAlarma->causante_id;
          }

    			$users = $role->users->filter(function($user) use($causante_id) {
            return $user->causante_id == $causante_id;
          });
    		} else {
          $users = collect();
        	$users_contrato = $solicitud->contrato->users_contratos_vigentes;
          foreach ($users_contrato as $keyUser => $valueUser) {
            $users->push($valueUser->user_publico->user);
          }
        }

        foreach ($users as $keyUser => $valueUser) {
          if($valueUser != null)
            $valueUser->sendSolicitudNotification($valueAlarma, $solicitud->id);
        }
      }
    }

}
