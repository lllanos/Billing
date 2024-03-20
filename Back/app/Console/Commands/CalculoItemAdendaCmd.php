<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;

use App\Jobs\CalculoItemAdenda;

class CalculoItemAdendaCmd extends Command {
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'yacyreta:calculo:item_adenda
                            {--id= : Opcional|Id de AnalisisItem, si no se especifica son todos los nuevos}';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Cálculos de Precios de Item agregado por Adenda de Ampliación';

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
      $analisis_item_id = $this->option('id');
      if($analisis_item_id != "" && $analisis_item_id != null)
        dispatch((new CalculoItemAdenda($analisis_item_id))->onQueue('calculos_variacion'));
      else
        dispatch((new CalculoItemAdenda())->onQueue('calculos_variacion'));
    }
}
