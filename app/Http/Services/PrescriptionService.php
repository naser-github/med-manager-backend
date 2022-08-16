<?php

namespace App\Http\Services;

use App\Models\Medicine;
use App\Models\Prescription;
use Illuminate\Support\Facades\Auth;

class PrescriptionService
{
    public function addToPrescription($medicineId,$timePeriod)
    {
        $prescription = new Prescription();
        $prescription->fk_user_id = Auth::id();
        $prescription->fk_medicine_id = $medicineId;
        $prescription->time_period = $timePeriod;
        $prescription->save();

        return $prescription;
    }

    public function index(): array
    {
        return Prescription::query()->where('prescriptions.fk_user_id', Auth::id())
            ->select('medicines.name', 'prescriptions.id', 'prescriptions.status', 'prescriptions.time_period')
            ->leftJoin('medicines', 'medicines.id', '=', 'prescriptions.fk_medicine_id')
            ->get();
    }

    public function prescriptionExist($payload)
    {
        return Prescription::where('fk_user_id', Auth::id())
            ->leftJoin('medicines', 'medicines.id', '=', 'prescriptions.fk_medicine_id')
            ->where('medicines.name', $payload)
            ->first();
    }

}
