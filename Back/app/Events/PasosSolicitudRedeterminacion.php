<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

use SolicitudRedeterminacion\SolicitudRedeterminacion;
use SolicitudRedeterminacion\Instancia\TipoInstanciaRedet;

class PasosSolicitudRedeterminacion {
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $solicitud;
    public $desencadenante_id;
    public $correccion;

    /**
     * Create a new event instance.
     * @return void
     * @param  SolicitudRedeterminacion  $solicitud
     * @param  TipoInstanciaRedet  $tipo_instancia
     */
    public function __construct(SolicitudRedeterminacion $solicitud, TipoInstanciaRedet $tipo_instancia, $correccion) {
      $this->solicitud = $solicitud;
      $this->desencadenante_id = $tipo_instancia->id;
      $this->correccion = $correccion;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn() {
      return new PrivateChannel('channel-name');
    }
}
