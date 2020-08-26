<?php

namespace App\Services\ETL;

use Illuminate\Support\Str;
use App\Exports\VendorsExport;
use Illuminate\Support\Collection;
use Illuminate\Console\OutputStyle;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Console\Concerns\InteractsWithIO;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\StreamOutput;

abstract class ETLService implements ETLServiceInterface
{
    use InteractsWithIO;

    public function __construct() {
        $this->setOutput(new OutputStyle(
            new StringInput(''),
            new StreamOutput(fopen('php://stdout', 'w'))
        ));
    }

    /**
     * Export destination.
     *
     * @var string
     */
    CONST DISK = 'exports';

    protected $title;

    /**
     * Return the title of the service.
     *
     * @return string
     */
    public function title(): string
    {
        return $this->title;
    }

    /**
     * Return the name of the file to be exported.
     *
     * @return string
     */
    public function filename(): string
    {
        return Str::slug($this->title) . '.csv';
    }

    /**
     * Store imported data to local disk.
     *
     * @param Collection $data
     * @return void
     */
    public function store(Collection $data)
    {
        $filename = $this->filename();

        Excel::store(new VendorsExport($data), $filename, static::DISK);

        return $this->path($filename);
    }

    /**
     * Return the path where the file will be exported.
     *
     * @return void
     */
    public function path($filename = '')
    {
        return Storage::disk(static::DISK)->path($filename);
    }

    /**
     * Run the service.
     *
     * @return void
     */
    abstract public function run();
}
