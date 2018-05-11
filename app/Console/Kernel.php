<?php

namespace Crawler\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Routing\Route;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
        // ANEEL
        $schedule->call('Crawler\Http\Controllers\AneelController@proInfa')->everyMinute();
        $schedule->call('Crawler\Http\Controllers\AneelController@contaDesenvEnerg')->everyMinute();
        $schedule->call('Crawler\Http\Controllers\AneelController@cdeAudiencia')->everyMinute();
        // ONS
        $schedule->call('Crawler\Http\Controllers\OnsController@sdroSemanal')->everyMinute();
        $schedule->call('Crawler\Http\Controllers\OnsController@sdroDiario')->everyMinute();
        $schedule->call('Crawler\Http\Controllers\OnsController@operacaoEnasDiario')->everyMinute();
        $schedule->call('Crawler\Http\Controllers\OnsController@getAcervoDigitalIpdoDiario')->everyMinute();
        $schedule->call('Crawler\Http\Controllers\OnsController@getAcervoDigitalPmoSemanal')->everyMinute();
        // CCEE
        $schedule->call('Crawler\Http\Controllers\CceeController@historicoPrecoSemanal')->everyMinute();
        $schedule->call('Crawler\Http\Controllers\CceeController@getInfoMercadoGeralAndIndividual')->everyMinute();
        $schedule->call('Crawler\Http\Controllers\CceeController@historicoPrecoMensal')->everyMinute();
        // ELETROBRAS
        $schedule->call('Crawler\Http\Controllers\EletroBrasController@getCde')->everyMinute();
        //EPE
        $schedule->call('Crawler\Http\Controllers\EpeConsumoController@getConsumo')->everyMinute();

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
