<?php

namespace App\Services\ETL;

class CNB extends ETLService
{
    protected $title = 'Puntos CNB';

    /**
     * Run the service.
     *
     * @return void
     */
    public function run()
    {
        // 1. Extract
        $this->warn(trans('etl.extracting'));
        $jsonString = file_get_contents(storage_path('app/imports/cnb.json'));
        $items = json_decode($jsonString);

        // 2. Transform
        $this->warn(trans('etl.transforming'));

        $this->output->progressStart(count($items));

        $items = collect($items)->map(function ($item) {
            $title = $item->titulo;

            $item->titulo = collect([
                $item->institucion,
                $item->localidad,
                'Corresponsal No Bancario'
            ])->implode(' - ');

            $item->descripcion = collect([
                $title,
                $item->descripcion,
            ])->implode(' - ');

            $lat = $item->lat;
            $lon = $item->lon;

            if (!$lat && !$lon) return null;

            if (!$lon) {
                list($lat, $lon) = explode(',', $lat);
            }

            $lat = str_replace(',', '', $lat) + 0;
            $lon = str_replace(',', '', $lon) + 0;

            if (is_integer($lat)) {
                $decimals = strlen($lat) - 3;
                $lat = $lat / (10 ** $decimals);
            }

            if (is_integer($lon)) {
                $decimals = strlen($lon) - 3;
                $lon = $lon / (10 ** $decimals);
            }

            $item->lat = $lat;
            $item->lon = $lon;

            $item->agreement = 'Sí, estoy de acuerdo.';

            $this->output->progressAdvance();

            return $item;
        });

        $items = $items->filter();

        $this->output->progressFinish();

        // 3. Load
        $this->warn(trans('etl.loading'));
        $filepath = $this->store($items);

        $this->info(PHP_EOL . "Archivo exportado: {$filepath}");
    }
 }
