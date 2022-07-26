<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Models\Prescription;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Traits\HelperFunctionTrait;

class PrescriptionController extends Controller
{
    use HelperFunctionTrait;

    public function addPrescription(Request $request)
    {
        request()->validate([
            'formData' => 'required',
        ]);

        $formData = $request->input('formData');
        $prescriptionNotSaved = array();

        foreach ($formData as $data) {

            $timePeriod = Carbon::today()->addDays($data['timePeriod'])->toDateString();

            $medicine = $this->medicineExist($data['medicineName']);

            // add medicine name to the medicine table if it doesn't exist
            if (!$medicine) {
                $medicine = new Medicine();
                $medicine->name = $data['medicineName'];
                $medicine->save();
            }

            $prescriptionExist = Prescription::where('fk_user_id', Auth::id())
                ->leftJoin('medicines', 'medicines.id', '=', 'prescriptions.fk_medicine_id')
                ->where('medicines.name', $data['medicineName'])
                ->first();

            if (!$prescriptionExist) {
                $prescription = new Prescription();
                $prescription->fk_user_id = Auth::id();
                $prescription->fk_medicine_id = $medicine->id;
                $prescription->time_period = $timePeriod;
                $prescription->save();

                $this->addDosage($prescription,$data['doseDetails']);
            } else {
                if ($prescriptionExist->time_period < Carbon::today() || $prescriptionExist->status = 'inactive') {
                    $prescriptionExist->time_period = $timePeriod;
                    $prescriptionExist->status = 'active';
                    $prescriptionExist->save();

                    $prescriptionExist->dose()->delete();

                    $this->addDosage($prescriptionExist,$data['doseDetails']);
                } else {
                    $prescriptionNotSaved[] = $data['medicineName'];
                }

            }
        }

        $response = [
            'msg' => 'medicines has been added',
            'prescriptionNotSaved' => $prescriptionNotSaved,
        ];

        return response($response, 201);
    }

    public function prescriptionList()
    {
        $list = Prescription::where('prescriptions.fk_user_id', Auth::id())
            ->select('medicines.name', 'prescriptions.id', 'prescriptions.status', 'prescriptions.time_period')
            ->leftJoin('medicines', 'medicines.id', '=', 'prescriptions.fk_medicine_id')
            ->get();

        $response = [
            'list' => $list,
        ];

        return response($response, 200);
    }

    public function updatePrescription(Request $request)
    {

        $prescriptionExist = Prescription::where('id', $request->id)->first();

        if ($prescriptionExist) {
            $medicine = Medicine::where('name', $request->name)->first();

            if (!$medicine) {
                $medicine = new Medicine();
                $medicine->name = $request->name;
                $medicine->save();
            }

        } else {
            $response = [
                'msg' => "medicines doesn't exist",
            ];

            return response($response, 404);
        }
    }
}
