<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\VisualizationService;
use Illuminate\View\View;

class VisualizationController extends Controller
{
    public function index(
        VisualizationService $service
    ): View {

        return view(

            'user.visualization.index',

            $service->getDashboardData()

        );

    }
}