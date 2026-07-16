<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Port;
use App\Services\PortService;
use Illuminate\View\View;

class PortController extends Controller
{
    public function index(
    PortService $portService
): View
{
    return view('user.ports.index', [

        'ports' => $portService->getPorts(request()->all()),

        'countries' => $portService->getCountries(),

        'portsMap' => $portService->getPortsMap(),

    ]);
}

    public function show(
        Port $port
    ): View
    {
        $port->load('country');

        return view('user.ports.show', compact('port'));
    }
}