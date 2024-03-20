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

class NewSolicitudRedeterminacion {
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $solicitud;

    public function __construct(SolicitudRedeterminacion $solicitud) {
        $this->solicitud = $solicitud;
    }

}
