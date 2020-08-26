<?php

namespace App\Services\ETL;

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

        // 2. Transform
        $this->warn(trans('etl.transforming'));

        // 3. Load
        $this->warn(trans('etl.loading'));
    }
 }
