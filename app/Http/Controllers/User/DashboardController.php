<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\UserDashboardService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected UserDashboardService $dashboardService
    ) {
    }

    public function index(): View
    {
        return view(
            'user.dashboard.index',
            $this->dashboardService->getDashboardData()
        );
    }
}