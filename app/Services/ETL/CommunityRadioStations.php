<?php

namespace App\Services\ETL;

use Zero\Http\Client;

class CommunityRadioStations extends ETLService
{
    protected $title = 'Radios Comunitarias';

    /**
     * Run the service.
     *
     * @return void
     */
    public function run()
    {
        // 1. Extract
        $this->warn(trans('etl.extracting'));
        $client = new Client('http://umap.openstreetmap.fr/en/');
        $response = $client->get('datalayer/950387/');

        // 2. Transform
        $this->warn(trans('etl.transforming'));
        $features = collect($response->features)->map(function ($feature) {

            list($lon, $lat) = $feature->geometry->coordinates;

            return json_decode(json_encode([
                'id' => $feature->properties->indicativo,
                'channel' => $feature->properties->canal,
                'name' => implode(" - ", [
                    "{$feature->properties->nombre}",
                    "{$feature->properties->indicativo}",
                    "{$feature->properties->frecuencia}MHz",
                ]),
                'authorized' => $feature->properties->autorizado,
                'description' => implode("<br>", [
                    "Departamento: {$feature->properties->departamento}",
                    "Localidad: {$feature->properties->localidad}",
                    "Frecuencia: {$feature->properties->frecuencia}MHz",
                    "P.E.R.: {$feature->properties->per}W",
                ]),
                'department' => $feature->properties->departamento,
                'city' => $feature->properties->localidad,
                'source' => 'CONATEL',
                'link' => "http://umap.openstreetmap.fr/en/map/pymc_349107#16/{$lat}/{$lon}",
                'lat' => $lat,
                'lon' => $lon,
                'agreement' => 'SI, Acepto',
                'category' => 'Radios Comunitarias',
            ]));
        });

        // 3. Load
        $this->warn(trans('etl.loading'));
        $filepath = $this->store($features);

        $this->info(PHP_EOL . "Archivo exportado: {$filepath}");
    }
}
