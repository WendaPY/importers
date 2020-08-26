<?php

namespace App\Services\ETL;

interface ETLServiceInterface
{
    /**
     * Return the title of the service.
     *
     * @return string
     */
    public function title(): string;

    /**
     * Run the service.
     *
     * @return void
     */
    public function run();
}
