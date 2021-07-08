<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class WendaETL extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wenda:etl';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Execute an ETL service to feed Wenda Map';

    /**
     * The application's global ETL services stack.
     *
     * @var array
     */
    protected $services = [
        \App\Services\ETL\ParaguayProducts::class,
        \App\Services\ETL\CommunityRadioStations::class,
        \App\Services\ETL\ParaguayPots::class,
        \App\Services\ETL\ConcepcionCollege::class,
        \App\Services\ETL\BNF::class,
        \App\Services\ETL\FreeWiFi::class,
        \App\Services\ETL\CNB::class,
        \App\Services\ETL\NationalSMB::class,
    ];

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Show service list
        $index = array_search(
            $this->choice('¿Los datos de qué servicio desea importar?', $this->serviceList()),
            $this->serviceList()
        );

        // Run selected option
        $service = $this->resolveService($index);
        $service->run();

        return 0;
    }

    public function serviceList(): array
    {
        $serviceList = collect($this->services)->map(function ($item) {
            return (new $item)->title();
        });

        return $serviceList->toArray();
    }

    public function resolveService($index)
    {
        $service = $this->services[$index];

        return new $service;
    }
}
