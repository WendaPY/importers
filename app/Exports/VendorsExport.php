<?php

namespace App\Exports;

use App\Models\Vendor;
use Illuminate\Support\Collection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class VendorsExport implements FromView
{
    public function __construct(Collection $data) {
        $this->data = $data;
    }

    public function view(): View
    {
        return view('exports/vendors', [
            'headers' => array_keys((array) $this->data->first()),
            'data' => $this->data,
        ]);
    }
}
