<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
      \App\Console\Commands\CalculoPreciosCmd::class,
      \App\Console\Commands\InstanciaCalculoPreciosCmd::class,
      \App\Console\Commands\CalculoVariacionEnPublicacionCmd::class,
      \App\Console\Commands\CheckDaemonCommand::class,
      \App\Console\Commands\PasarInstanciasCmd::class,
      \App\Console\Commands\PoderesCmd::class,
      // \App\Console\Commands\ScheduleList::class,
      // \App\Console\Commands\SolicitudPorEstadoCmd::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule) {
       $schedule->command('yacyreta:poderes:vencimiento')
                ->dailyAt(config('custom.hora_proceso') . ':' . config('custom.min_proceso'));

       $schedule->command('yacyreta:calculo:variacion')
                ->dailyAt(config('custom.hora_proceso') . ':' . config('custom.min_proceso'));

       $schedule->command('yacyreta:calculo:precios')
                ->dailyAt(config('custom.hora_proceso') . ':' . config('custom.min_proceso'));

       $schedule->command('yacyreta:calculo:instancia_precios')
                ->dailyAt(config('custom.hora_proceso') . ':' . config('custom.min_proceso'));

       $schedule->command('yacyreta:calculo:item_adenda')
                ->dailyAt(config('custom.hora_proceso') . ':' . config('custom.min_proceso'));

       $schedule->command('yacyreta:solicitudes:pasar_instancia')
                ->dailyAt(config('custom.hora_proceso') . ':' . config('custom.min_proceso'));

       $schedule->command('clean:directories')
                ->dailyAt(config('custom.hora_proceso') . ':' . config('custom.min_proceso'));

      $schedule->command('yacyreta:check:daemon')
               ->hourly();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
