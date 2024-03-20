<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;

use App\Jobs\CalculoPrecios;

class CalculoPreciosCmd extends Command {
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'yacyreta:calculo:precios
                            {--id= : Opcional|Id de Salto, si no se especifica son todos}';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Cálculos de Nuevos Precios, posterior al de saltos';

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
      $salto_id = $this->option('id');
      if($salto_id != "" && $salto_id != null)
        dispatch((new CalculoPrecios($salto_id))->onQueue('calculos_variacion'));
      else
        dispatch((new CalculoPrecios())->onQueue('calculos_variacion'));
    }
}
