<?php

namespace App\Services\ETL;

use Zero\Http\Client;

class NationalSMB extends ETLService
{
    protected $title = 'Premio Nacional MiPymes';

    /**
     * Run the service.
     *
     * @return void
     */
    public function run()
    {
        // 1. Extract
        $this->warn(trans('etl.extracting'));
        $jsonString = file_get_contents(storage_path('app/imports/national_smb.json'));
        $items = json_decode($jsonString);

        // 2. Transform
        $this->warn(trans('etl.transforming'));

        $this->output->progressStart(count($items));
        
        $items = collect($items)->map(function ($item) {
            $item = json_decode(json_encode([
                'id' => $item->Id,
                'institucion' => $item->institucion,
                'titulo' => $item->titulo,
                'descripcion' => $item->descripcion,
                'rubro' => $item->Rubro,
                'lat' => $item->lat,
                'lon' => $item->lon,
                'responsable' => $item->responsable,
                'telefono' => $item->telefono,
                'email' => $item->email,
                'consentimiento' => 'Sí, acepto',
                'web_social_net' => collect([
                    $item->web,
                    $item->Facebook,
                    $item->instagram,
                    $item->twitter,
                    $item->linkeding,
                ])->filter()->implode(' - '),
                'departamento' => $item->departamento,
                'localidad' => $item->localidad,
                'fuente' => $item->institucion,
                'link_fuente' => 'https://www.mic.gov.py/',
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
