<?php

namespace App\Services\ETL;

use Zero\Http\Client;

class BNF extends ETLService
{
    protected $title = 'BNF';

    /**
     * Run the service.
     *
     * @return void
     */
    public function run()
    {
        // 1. Extract
        $this->warn(trans('etl.extracting'));
        $jsonString = file_get_contents(storage_path('app/imports/bnf.json'));
        $items = json_decode($jsonString);

        // 2. Transform
        $this->warn(trans('etl.transforming'));

        $this->output->progressStart(count($items));
        $items = collect($items)->map(function ($item) {

            $decimals = strlen($item->Latitud) - 3;
            $lat = $item->Latitud / (10 ** $decimals);
            $item->Latitud = $lat;

            $decimals = strlen($item->Longitud) - 3;
            $lon = $item->Longitud / (10 ** $decimals);
            $item->Longitud = $lon;

            $this->output->progressAdvance();

            return $item;
        });

        $this->output->progressFinish();

        // 3. Load
        $this->warn(trans('etl.loading'));
        $filepath = $this->store($items);

        $this->info(PHP_EOL . "Archivo exportado: {$filepath}");
    }
 }
