<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Models\Prescription;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PrescriptionController extends Controller
{
    public function addPrescription(Request $request)
    {
        request()->validate([
            'formData' => 'required',
        ]);

        $formData = $request->input('formData');
        $prescriptionNotSaved = array();


        foreach ($formData as $data) {

            $timePeriod = Carbon::today()->addDays($data['timePeriod'])->toDateString();

            $medicine = Medicine::where('name', $data['medicineName'])->first();

            if (!$medicine) {
                $medicine = new Medicine();
                $medicine->name = $data['medicineName'];
                $medicine->save();
            }

            $prescriptionExist = Prescription::where('fk_user_id', Auth::id())
                ->leftJoin('medicines', 'medicines.id', '=', 'prescriptions.fk_medicine_id')
                ->where('medicines.name', $data['medicineName'])
                ->where('time_period', '>=', Carbon::today())
                ->first();

            if ($prescriptionExist)
                $prescriptionNotSaved[] = $data['medicineName'];
            else {
                $prescription = new Prescription();
                $prescription->fk_user_id = Auth::id();
                $prescription->fk_medicine_id = $medicine->id;
                $prescription->time_period = $timePeriod;
                $prescription->save();

                foreach ($data['doseDetails'] as $dose) {
                    $prescription->dose()->create([
                        'label' => $dose['label'],
                        'time' => $dose['time'],
                    ]);
                }
            }
        }

        $response = [
            'msg' => 'medicines has been added',
            'prescriptionNotSaved' => $prescriptionNotSaved,
        ];

        return response($response, 201);
    }
}
