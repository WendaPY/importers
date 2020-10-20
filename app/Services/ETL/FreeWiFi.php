<?php

namespace App\Services\ETL;

use stdClass;
use Zero\Http\Client;

class FreeWiFi extends ETLService
{
    protected $title = 'IGEP - Internet Gratuito en Espacios Públicos';

    /**
     * Run the service.
     *
     * @return void
     */
    public function run()
    {
        // 1. Extract
        $this->warn(trans('etl.extracting'));
        $jsonString = file_get_contents(storage_path('app/imports/igep.json'));
        $items = json_decode($jsonString);

        // 2. Transform
        $this->warn(trans('etl.transforming'));

        $this->output->progressStart(count($items));
        $items = collect($items)->map(function ($item) {

            list($lat, $lon) = explode(',', $item->location);

            $point = new stdClass;
            $point->institute= "MITIC - Ministerio de Tecnología de la Información y Comunicación";
            $point->title = "Internet Gratuito - {$item->place}";
            $point->description = "Internet Gratuito en espacios públicos - {$item->place}";
            $point->lat = trim($lat);
            $point->lon = trim($lon);
            $point->phone = "+595212179000";
            $point->email = "comunicacion@mitic.gov.py";
            $point->link = "https://www.mitic.gov.py/viceministerios/tecnologias-de-la-informacion-y-comunicacion/wifi-libre";
            $point->department = $item->department;
            $point->city = $item->city;
            $point->source = "MITIC";
            $point->sourceLink = "https://www.mitic.gov.py/";

            $this->output->progressAdvance();

            return $point;
        });

        $this->output->progressFinish();

        // 3. Load
        $this->warn(trans('etl.loading'));
        $filepath = $this->store($items);

        $this->info(PHP_EOL . "Archivo exportado: {$filepath}");
    }
 }
