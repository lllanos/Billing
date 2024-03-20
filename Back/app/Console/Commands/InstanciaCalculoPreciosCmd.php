<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;

use App\Jobs\InstanciaCalculoPrecios;

class InstanciaCalculoPreciosCmd extends Command {
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'yacyreta:calculo:instancia_precios
                            {--id= : Opcional|Id de la solicitud, si no se especifica son todas}';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Segunda parte de Cálculos de Nuevos Precios, cuando se pasa a la instancia correspondiente';

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
      $id_solicitud = $this->option('id');
      if($id_solicitud != "" && $id_solicitud != null)
        dispatch((new InstanciaCalculoPrecios($id_solicitud))->onQueue('calculos_variacion'));
      else
        dispatch((new InstanciaCalculoPrecios())->onQueue('calculos_variacion'));
    }
}
