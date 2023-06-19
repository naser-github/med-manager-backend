<?php

namespace App\Http\Controllers\Medicine;

use App\Http\Controllers\Controller;
use App\Http\Resources\Medicine\MedicineResource;
use App\Http\Services\MedicineService;

class MedicineController extends Controller
{
    public function search($searchingBy, MedicineService $medicineService)
    {
        $medicine = $medicineService->searchByName($searchingBy);

        return response()->json([
            'success' => true,
            'result' => MedicineResource::collection($medicine),
        ], 200);
    }
}
