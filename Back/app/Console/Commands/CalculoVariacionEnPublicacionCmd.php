<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;

use App\Jobs\CalculoVariacionEnPublicacion;

class CalculoVariacionEnPublicacionCmd extends Command {
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'yacyreta:calculo:variacion
                            {--id= : Opcional|Id del contrato, si no se especifica son todos}';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Cálculos de Variación';

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
      $contrato_id = $this->option('id');
      if($contrato_id != "" && $contrato_id != null)
        dispatch((new CalculoVariacionEnPublicacion($contrato_id))->onQueue('calculos_variacion'));
      else
        dispatch((new CalculoVariacionEnPublicacion())->onQueue('calculos_variacion'));
    }
}
