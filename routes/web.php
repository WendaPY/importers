<?php

use App\Models\Vendor;
use App\Models\Product;
use App\Exports\VendorsExport;
use App\Libraries\Menu\ProductsPy;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/export', function () {
    return Excel::download(new VendorsExport, 'vendors.csv');
});

Route::get('/', function () {
    $client = new ProductsPy;

    $vendors = $client->vendors();
    $vendorCollection = collect();
    $productCollection = collect();

    collect($vendors->features)->each(function ($feature) use ($vendorCollection, $productCollection) {
        list($lon, $lat) = $feature->geometry->coordinates;

        $phone = $feature->properties->contacto;
        $comments = $feature->properties->comentarios;
        $concat = "Fuente: ProductosPY (https://productospy.org/)";
        $name = $feature->properties->nombre;
        $products = collect($feature->properties->productos)->pluck('product_name')->implode(' / ');

        collect($feature->properties->productos)->each(function ($product) use ($feature, $productCollection) {
            $productCollection->push([
                'vendor_id' => $feature->properties->id,
                'name' => $product->product_name,
                'code' => $product->product_type,
            ]);
        });

        $vendorCollection->push([
            'id' => $feature->properties->id,
            'name' => is_null($name) || strlen(trim($name)) == 0 ? (strlen($products) > 50 ? substr($products, 0, 50) . '...' : $products) : $name,
            'phone' => $phone,
            'comments' => is_null($comments) || strlen(trim($comments)) == 0 ? $concat : "{$comments}\n\n{$concat}",
            'lat' => $lat,
            'lon' => $lon,
        ]);
    });

    Product::truncate();
    Vendor::truncate();
    Vendor::insert($vendorCollection->toArray());
    Product::insert($productCollection->toArray());

    return 'Done';
});
