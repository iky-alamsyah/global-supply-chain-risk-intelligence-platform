<?php

namespace App\Services;

use App\Models\Country;
use App\Models\Port;

class PortService
{
    public function getPorts(array $filters = [])
    {
        $query = Port::with('country');

        if (!empty($filters['search'])) {

            $query->where(function ($q) use ($filters) {

    $q->where('port_name', 'like', '%' . $filters['search'] . '%')
      ->orWhere('port_code', 'like', '%' . $filters['search'] . '%')
      ->orWhere('city', 'like', '%' . $filters['search'] . '%')
      ->orWhereHas('country', function ($country) use ($filters) {

            $country->where(
                'name',
                'like',
                '%' . $filters['search'] . '%'
            );

      });

});

        }

        if (!empty($filters['country'])) {

            $query->whereHas('country', function ($q) use ($filters) {

                $q->where('id', $filters['country']);

            });

        }

        return $query
            ->orderBy('port_name')
            ->paginate(20)
            ->withQueryString();
    }

    public function getCountries()
    {
        return Country::orderBy('name')->get();
    }

    public function getPortsMap()
{
    return Port::with('country')
        ->get()
        ->map(function ($port) {

            return [

                'name' => $port->port_name,

                'country' => $port->country->name,

                'lat' => $port->latitude,

                'lng' => $port->longitude,

                'status' => $port->status,

                'code' => $port->port_code,

            ];

        });
}
}