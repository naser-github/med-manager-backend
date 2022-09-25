<?php

namespace App\Http\Services;

use App\Models\Medicine;
use App\Models\Prescription;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use function Symfony\Component\Translation\t;

class PrescriptionService
{
    public function addToPrescription($medicineId, $timePeriod)
    {
        $prescription = new Prescription();
        $prescription->fk_user_id = Auth::id();
        $prescription->fk_medicine_id = $medicineId;
        $prescription->time_period = $timePeriod;
        $prescription->save();

        return $prescription;
    }

    public function dosageDetails($payload)
    {
        return Prescription::query()
            ->where('fk_medicine_id', $payload)
            ->where('fk_user_id', Auth::id())
            ->leftJoin('dosages', 'dosages.dosage_id', '=', 'prescriptions.id')
            ->leftJoin('medicines', 'medicines.id', '=', 'prescriptions.fk_medicine_id')
            ->select('dosages.id', 'medicines.name', 'dosages.label', 'dosages.time', 'prescriptions.time_period', 'dosages.status')
            ->get();
    }

    public function findById($payload)
    {
        return Prescription::query()->where('fk_user_id', Auth::id())->where('id', $payload)->first();
    }

    public function findPrescribedMedicine($payload)
    {
        return Prescription::query()
            ->where('fk_user_id', Auth::id())
            ->where('fk_medicine_id', $payload)
            ->with(['medicine', 'dose'])
            ->first();
    }

    public function index()
    {
        return Prescription::query()->where('prescriptions.fk_user_id', Auth::id())
            ->select('medicines.name', 'prescriptions.id', 'prescriptions.status', 'prescriptions.time_period')
            ->leftJoin('medicines', 'medicines.id', '=', 'prescriptions.fk_medicine_id')
            ->get();
    }

    public function findByPrescriptionMedicineName($payload)
    {
        return Prescription::where('fk_user_id', Auth::id())
            ->leftJoin('medicines', 'medicines.id', '=', 'prescriptions.fk_medicine_id')
            ->where('medicines.name', $payload)
            ->first();
    }

    public function updatePrescription($prescription, $medicineId, $payload)
    {
        $prescription->fk_medicine_id = $medicineId;
        $prescription->time_period = Carbon::today()->addDays($payload['time_period'])->toDateString();
        $prescription->status = $payload['status'];

        $prescription->save();
    }

}
