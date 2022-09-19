<?php

namespace App\Http\Controllers\Prescription;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Request;
use App\Http\Requests\Prescription\PrescriptionAddRequest;
use App\Http\Resources\Prescription\PrescriptionResource;
use App\Http\Services\MedicineService;
use App\Http\Services\PrescriptionService;
use App\Http\Traits\HelperFunctionTrait;
use App\Models\Prescription;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PrescriptionController extends Controller
{
    use HelperFunctionTrait;

    /**
     * @param PrescriptionAddRequest $request
     * @param MedicineService $medicineService
     * @param PrescriptionService $prescriptionService
     * @return JsonResponse
     */
    public function addPrescription(PrescriptionAddRequest $request, MedicineService $medicineService, PrescriptionService $prescriptionService): JsonResponse
    {
        $validatedData = $request->validated();

        $prescriptionNotSaved = array();

        DB::beginTransaction();
        try {

            foreach ($validatedData['formData'] as $data) {

                $timePeriod = Carbon::today()->addDays($data['timePeriod'])->toDateString(); // converting $data['timePeriod'] is an integer which is getting date

                $medicine = $medicineService->findByName($data['medicineName']); // checks if medicine already exist in the database

                if (!$medicine) $medicine = $medicineService->create($data['medicineName']); // add medicine to the medicine table if it doesn't exist

                $prescriptionExist = $prescriptionService->prescriptionExist($data['medicineName']); // checks if same medicine already running for a user


                if (!$prescriptionExist) {
                    $prescription = $prescriptionService->addToPrescription($medicine->id, $timePeriod); // adding medicine for that user

                    $this->addDosage($prescription, $data['doseDetails']); // addDosage is a function from HelperFunctionTrait
                } else {
                    // if medicine exist checks its status & update it
                    if ($prescriptionExist->time_period < Carbon::today() || $prescriptionExist->status == 'inactive') {
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
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'medicines has been added',
                'prescriptionNotSaved' => $prescriptionNotSaved,
            ], 201);
        } catch (\Exception $error) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => 'consumption failed' . $error,], 500);
        }

    }

    /**
     * @param PrescriptionService $prescriptionService
     * @return JsonResponse
     */
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
