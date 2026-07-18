<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Port;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class AdminPortController extends Controller
{
    /**
     * Display a listing of the ports.
     */
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $countryId = $request->input('country_id');
        $statusFilter = $request->input('status');

        // Statistics
        $totalPorts = Port::count();
        $activePorts = Port::where('status', 'active')->count();
        $inactivePorts = $totalPorts - $activePorts;

        // Query builder
        $query = Port::with('country');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('port_name', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('port_code', 'like', "%{$search}%")
                  ->orWhereHas('country', function ($c) use ($search) {
                      $c->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($countryId) {
            $query->where('country_id', $countryId);
        }

        if ($statusFilter) {
            $query->where('status', $statusFilter);
        }

        $ports = $query->orderBy('port_name', 'asc')->paginate(10)->withQueryString();
        $countries = Country::orderBy('name')->get();

        return view('admin.ports.index', compact(
            'ports', 'countries', 'totalPorts', 'activePorts', 'inactivePorts',
            'search', 'countryId', 'statusFilter'
        ));
    }

    /**
     * Show the form for creating a new port.
     */
    public function create(): View
    {
        $countries = Country::orderBy('name')->get();
        return view('admin.ports.create', compact('countries'));
    }

    /**
     * Store a newly created port in database.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'country_id' => ['required', 'exists:countries,id'],
            'port_name' => ['required', 'string', 'max:255'],
            'port_code' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'timezone' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'string', Rule::in(['active', 'inactive'])],
            'description' => ['nullable', 'string'],
        ]);

        Port::create($request->only([
            'country_id', 'port_name', 'port_code', 'city', 'latitude', 'longitude', 'timezone', 'status', 'description'
        ]));

        return redirect()->route('admin.ports.index')->with('success', 'Port created successfully.');
    }

    /**
     * Display the specified port.
     */
    public function show(Port $port): View
    {
        $port->load('country');
        return view('admin.ports.show', compact('port'));
    }

    /**
     * Show the form for editing the specified port.
     */
    public function edit(Port $port): View
    {
        $countries = Country::orderBy('name')->get();
        return view('admin.ports.edit', compact('port', 'countries'));
    }

    /**
     * Update the specified port in database.
     */
    public function update(Request $request, Port $port): RedirectResponse
    {
        $request->validate([
            'country_id' => ['required', 'exists:countries,id'],
            'port_name' => ['required', 'string', 'max:255'],
            'port_code' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'timezone' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'string', Rule::in(['active', 'inactive'])],
            'description' => ['nullable', 'string'],
        ]);

        $port->update($request->only([
            'country_id', 'port_name', 'port_code', 'city', 'latitude', 'longitude', 'timezone', 'status', 'description'
        ]));

        return redirect()->route('admin.ports.index')->with('success', 'Port updated successfully.');
    }

    /**
     * Remove the specified port from database.
     */
    public function destroy(Port $port): RedirectResponse
    {
        $port->delete();
        return redirect()->route('admin.ports.index')->with('success', 'Port deleted successfully.');
    }
}
