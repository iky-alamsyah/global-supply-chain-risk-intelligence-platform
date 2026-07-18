<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\ShipmentEstimationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class ShipmentEstimationController extends Controller
{
    public function __construct(
        protected ShipmentEstimationService $estimationService
    ) {
    }

    /**
     * Display the Shipment Route Estimation interface.
     */
    public function index(): View
    {
        $countries = $this->estimationService->getActiveCountries();
        return view('user.shipment-estimation.index', compact('countries'));
    }

    /**
     * Run the simulation estimation.
     */
    public function estimate(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'origin_country_id' => ['required', 'integer', 'exists:countries,id'],
                'dest_country_id' => [
                    'required',
                    'integer',
                    'exists:countries,id',
                    'different:origin_country_id',
                ],
                'cargo_type' => ['required', 'string', Rule::in(['General Cargo', 'Container', 'Bulk Cargo', 'Liquid Cargo', 'Vehicle'])],
                'ship_speed' => ['required', 'string', Rule::in(['slow', 'normal', 'fast'])],
            ], [
                'dest_country_id.different' => 'Negara tujuan harus berbeda dengan negara asal.',
            ]);

            $result = $this->estimationService->calculateEstimation($request->all());

            return response()->json([
                'success' => true,
                'data' => $result,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->validator->errors()->first(),
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
