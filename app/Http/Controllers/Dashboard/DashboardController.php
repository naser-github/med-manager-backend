<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Services\DashboardService;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    /**
     * @param DashboardService $dashboardService
     * @return JsonResponse
     */
    public function index(DashboardService $dashboardService): JsonResponse
    {
        $dailyDoseList = $dashboardService->dailyDoseList();

        return response()->json(['success' => true, 'dailyDoseList' => $dailyDoseList,], 200);
    }
}
