<?php

namespace App\Http\Controllers;

use App\Support\Services\ServiceCatalog;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function show(string $service): View
    {
        $serviceData = ServiceCatalog::findBySlug($service);

        abort_if($serviceData === null, 404);

        return view('services.show', [
            'service' => $serviceData,
            'services' => ServiceCatalog::all(),
        ]);
    }
}
