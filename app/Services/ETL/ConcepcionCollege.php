<?php

namespace App\Services\ETL;

use Zero\Http\Client;

class ConcepcionCollege extends ETLService
{
    protected $title = 'Universidad Nacional de Concepción';

    /**
     * Run the service.
     *
     * @return void
     */
    public function run()
    {
        // 1. Extract
        $this->warn(trans('etl.extracting'));
        $jsonString = file_get_contents(storage_path('app/imports/unc.json'));
        $items = json_decode($jsonString);

        // 2. Transform
        $this->warn(trans('etl.transforming'));

        $this->output->progressStart(count($items));
        $items = collect($items)->map(function ($item) {

            $item = json_decode(json_encode([
                'id' => $item->id,
                'title' => $item->rubro,
                'description' => $item->tipo,
                'fantasy' => $item->empresa,
                'business_name' => $item->empresa,
                'contact' => $item->propietario,
                'phone' => $item->telefono,
                'lat' => $item->lat,
                'lon' => $item->lon,
                'address' => $item->direccion,
                'category' => 'Productos y Servicios',
                'source' => 'Universidad Nacional de Concepción',
                'link' => 'www.unc.edu.py',
                'link' => 'www.unc.edu.py',
                'agreement' => 'SI, Acepto',
                'ODS' => 'ODS 1: Fin de la pobreza, ODS 8: Trabajo decente y crecimiento económico',
            ]));

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
