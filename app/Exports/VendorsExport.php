<?php

namespace App\Exports;

use App\Models\Vendor;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class VendorsExport implements FromView
{
    public function view(): View
    {
        return view('exports.vendors', [
            'vendors' => Vendor::all(),
        ]);
    }
}
