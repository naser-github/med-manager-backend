<?php

namespace App\Http\Services;

use App\Models\Medicine;
use App\Models\Prescription;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use function Symfony\Component\Translation\t;

class PrescriptionService
{
    /**
     * @param $medicineId
     * @param $timePeriod
     * @return Prescription
     */
    public function addToPrescription($medicineId, $timePeriod): Prescription
    {
        $prescription = new Prescription();
        $prescription->fk_user_id = Auth::id();
        $prescription->fk_medicine_id = $medicineId;
        $prescription->time_period = $timePeriod;
        $prescription->save();

        return $prescription;
    }

    /**
     * @param $payload
     * @return Collection|array
     */
    public function dosageDetails($payload): Collection|array
    {
        return Prescription::query()
            ->where('fk_medicine_id', $payload)
            ->where('fk_user_id', Auth::id())
            ->leftJoin('dosages', 'dosages.dosage_id', '=', 'prescriptions.id')
            ->leftJoin('medicines', 'medicines.id', '=', 'prescriptions.fk_medicine_id')
            ->select('dosages.id', 'medicines.name', 'dosages.label', 'dosages.time', 'prescriptions.time_period', 'dosages.status')
            ->get();
    }

    /**
     * @param $payload
     * @return Model|Builder|null
     */
    public function findById($payload): Model|Builder|null
    {
        return Prescription::query()->where('fk_user_id', Auth::id())->where('id', $payload)->first();
    }

    /**
     * @param $payload
     * @return Model|Builder|null
     */
    public function findPrescribedMedicine($payload): Model|Builder|null
    {
        return Prescription::query()
            ->where('fk_user_id', Auth::id())
            ->where('fk_medicine_id', $payload)
            ->with(['medicine', 'dose'])
            ->first();
    }

    /**
     * @param $payload
     * @return Model|Builder|null
     */
    public function findByPrescriptionMedicineName($payload): Model|Builder|null
    {
        return Prescription::query()->where('fk_user_id', Auth::id())
            ->leftJoin('medicines', 'medicines.id', '=', 'prescriptions.fk_medicine_id')
            ->where('medicines.name', $payload)
            ->first();
    }

    /**
     * @return Collection|array
     */
    public function index(): Collection|array
    {
        return Prescription::query()->where('prescriptions.fk_user_id', Auth::id())
            ->leftJoin('medicines', 'medicines.id', '=', 'prescriptions.fk_medicine_id')
            ->select(
                'medicines.id as medicine_id', 'medicines.name as medicine_name',
                'prescriptions.id', 'prescriptions.status', 'prescriptions.time_period'
            )
            ->get();
    }

    /**
     * @param $prescription
     * @param $medicineId
     * @param $payload
     * @return void
     */
    public function updatePrescription($prescription, $medicineId, $payload): void
    {
        $prescription->fk_medicine_id = $medicineId;
        $prescription->time_period = Carbon::today()->addDays($payload['time_period'] - 1)->toDateString();
        $prescription->status = $payload['status'];

        $prescription->save();
    }

}
