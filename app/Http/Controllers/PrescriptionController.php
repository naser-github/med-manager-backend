<?php

namespace App\Http\Controllers;

use App\Http\Requests\Prescription\PrescriptionAddRequest;
use App\Http\Resources\PrescriptionResource;
use App\Http\Services\MedicineService;
use App\Http\Services\PrescriptionService;
use Illuminate\Http\JsonResponse;
use App\Http\Traits\HelperFunctionTrait;
use Illuminate\Support\Carbon;

class PrescriptionController extends Controller
{
    use HelperFunctionTrait;

    public function addPrescription(PrescriptionAddRequest $request, MedicineService $medicineService, PrescriptionService $prescriptionService)
    {

        $request->validated();

        $formData = $request->input('formData');
        $prescriptionNotSaved = array();

        foreach ($formData as $data) {

            $timePeriod = Carbon::today()->addDays($data['timePeriod'])->toDateString();

            $medicine = $medicineService->findByName($data['medicineName']);


            // add medicine name to the medicine table if it doesn't exist
            if (!$medicine) $medicine = $medicineService->create($data['medicineName']);


            $prescriptionExist = $prescriptionService->prescriptionExist($data['medicineName']);

            if (!$prescriptionExist) {
                $prescription = $prescriptionService->addToPrescription($medicine->id, $timePeriod);

                $this->addDosage($prescription, $data['doseDetails']);
            } else {
                if ($prescriptionExist->time_period < Carbon::today() || $prescriptionExist->status = 'inactive') {
                    $prescriptionExist->time_period = $timePeriod;
                    $prescriptionExist->status = 'active';
                    $prescriptionExist->save();

                    $prescriptionExist->dose()->delete();

                    $this->addDosage($prescriptionExist, $data['doseDetails']);
                } else {
                    $prescriptionNotSaved[] = $data['medicineName'];
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'medicines has been added',
            'prescriptionNotSaved' => $prescriptionNotSaved,
        ], 201);

    }

    public function prescriptionList(PrescriptionService $prescriptionService): JsonResponse
    {
        $prescriptionList = PrescriptionResource::collection($prescriptionService->index());

        return response()->json([
            'success' => true,
            'prescriptionList' => $prescriptionList,
        ], 200);
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
