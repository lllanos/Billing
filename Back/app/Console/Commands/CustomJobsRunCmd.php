<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;

use Yacyreta\Jobs\JobCustom;

class CustomJobsRunCmd extends Command {
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'yacyreta:jobs:run';

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
      $i = 0;
      foreach (JobCustom::all() as $keyJob => $valueJob) {
        if($valueJob->available_at != $valueJob->created_at) {
          $valueJob->available_at = $valueJob->created_at;
          $valueJob->save();
          $i++;
        }
      }
      echo 'Se forzó la ejecución de ' . $i . ' jobs';
    }
}
