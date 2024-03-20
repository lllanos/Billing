<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;

use App\Jobs\VencimientoPoderes;

class PoderesCmd extends Command {
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'yacyreta:poderes:vencimiento';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Encolar Job VencimientoPoderes';

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
      dispatch((new VencimientoPoderes($args)));
    }
}
