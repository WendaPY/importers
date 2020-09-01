<?php

namespace App\Services\ETL;

use Zero\Http\Client;

class ParaguayPots extends ETLService
{
    protected $title = 'OllasPy';

    /**
     * Run the service.
     *
     * @return void
     */
    public function run()
    {
        // 1. Extract
        $this->warn(trans('etl.extracting'));
        $jsonString = file_get_contents(storage_path('app/imports/ollas.json'));
        $items = json_decode($jsonString);

        // 2. Transform
        $this->warn(trans('etl.transforming'));

        $this->output->progressStart(count($items));
        $items = collect($items)->map(function ($item) {
            $client = new Client('https://nominatim.openstreetmap.org/');
            $place = $client->get("reverse?format=json&lat={$item->lat}&lon={$item->lon}");

            $address = implode(", ", [
                $place->address->suburb,
                $place->address->city ?? $place->address->state,
            ]);

            $item->title = "{$item->title} - {$address}";

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
