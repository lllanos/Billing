<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;

use App\Jobs\PasarInstancias;

class PasarInstanciasCmd extends Command {
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'yacyreta:solicitudes:pasar_instancia';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Pasa de instancia todas las solicitudes que se quedaron en "Iniciada" o de "Verificación de Certificados" no asociados';

    /**
     * Create a new command instance.
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     * @return mixed
     */
    public function handle() {
      $args = '';
      dispatch((new PasarInstancias($args)));
    }
}
