<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Log;

class CheckDaemonCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'yacyreta:check:daemon';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Chequear si daemon de queue:work esta activo.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
      $is_running = shell_exec("ps -ef | grep php");
      if($is_running != null) {
        if (strpos($is_running, 'queue:work') !== false) {
          return 'running';
        }
        else {
          Log::error('CheckDaemonCommand Relanzado');
          // shell_exec("nohup php artisan queue:work --tries=1  --queue=default,redeterminaciones,calculos_variacion,poderes,publicaciones,contratos --daemon &");
          shell_exec("nohup php artisan queue:work --tries=1  --queue=default,redeterminaciones,calculos_variacion,poderes,publicaciones,contratos &");
        }
      }
      else {
        Log::error('CheckDaemonCommand Relanzado');
        shell_exec("nohup php artisan queue:work --tries=1  --queue=default,redeterminaciones,calculos_variacion,poderes,publicaciones,contratos &");
      }
    }
}
