<?php

namespace App\Services\ETL;

use Zero\Http\Client;

class ParaguayProducts extends ETLService
{
    protected $title = 'ProductosPy';

    /**
     * Run the service.
     *
     * @return void
     */
    public function run()
    {
        // 1. Extract
        $this->warn(trans('etl.extracting'));
        $client = new Client('https://productospy.org/api/');
        $response = $client->get('vendedores');

        // 2. Transform
        $this->warn(trans('etl.transforming'));

        $this->output->progressStart(count($response->features));
        $features = collect($response->features)->map(function ($feature) {
            list($lon, $lat) = $feature->geometry->coordinates;

            $phone = $feature->properties->contacto;
            $comments = $feature->properties->comentarios;
            $concat = "Fuente: ProductosPY (https://productospy.org/)";
            $name = $feature->properties->nombre;
            $products = collect($feature->properties->productos)->pluck('product_name')->implode(' / ');

            $item = json_decode(json_encode([
                'id' => $feature->properties->id,
                'name' => is_null($name) || strlen(trim($name)) == 0 ? (strlen($products) > 50 ? substr($products, 0, 50) . '...' : $products) : $name,
                'phone' => $phone,
                'comments' => is_null($comments) || strlen(trim($comments)) == 0 ? $concat : "{$comments}\n\n{$concat}",
                'products' => $products,
                'source' => 'ProductosPY',
                'link' => 'https://productospy.org/',
                'lat' => $lat,
                'lon' => $lon,
            ]));

            $this->output->progressAdvance();

            return $item;
        });

        $this->output->progressFinish();

        // 3. Load
        $this->warn(trans('etl.loading'));
        $filepath = $this->store($features);

        $this->info(PHP_EOL . "Archivo exportado: {$filepath}");
    }
 }
