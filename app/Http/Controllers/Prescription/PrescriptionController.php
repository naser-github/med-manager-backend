<?php

namespace App\Http\Controllers\Prescription;

use App\Http\Controllers\Controller;
use App\Http\Requests\Prescription\PrescriptionAddRequest;
use App\Http\Requests\Prescription\PrescriptionUpdateRequest;
use App\Http\Resources\Prescription\PrescribedMedicineResource;
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
        $validatedData = $request->validated(); // validates data

        $prescriptionNotSaved = array();

        DB::beginTransaction();
        try {

            foreach ($validatedData['formData'] as $data) {

                $timePeriod = Carbon::today()->addDays($data['timePeriod'])->toDateString(); // converting $data['timePeriod'] is an integer which is getting date

                $medicine = $medicineService->findByName($data['medicineName']); // checks if medicine already exist in the database

                if (!$medicine) $medicine = $medicineService->create($data['medicineName']); // add medicine to the medicine table if it doesn't exist

                $prescriptionExist = $prescriptionService->findByPrescriptionMedicineName($data['medicineName']); // checks if same medicine already running for a user


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
            return response()->json(['success' => false, 'message' => 'adding medicines to prescription failed' . $error,], 500);
        }

    }

    /**
     * @param PrescriptionService $prescriptionService
     * @return JsonResponse
     */
    public function prescriptionList(PrescriptionService $prescriptionService): JsonResponse
    {
        $prescriptionList = $prescriptionService->index(); // fetching prescription list of a user

        return response()->json([
            'success' => true,
            'prescriptionList' => PrescriptionResource::collection($prescriptionList),
        ], 200);
    }

    /**
     * @param $medicineId
     * @param PrescriptionService $prescriptionService
     * @return JsonResponse
     */
    public function editPrescription($medicineId, PrescriptionService $prescriptionService): JsonResponse
    {
        $prescribedMedicine = $prescriptionService->findPrescribedMedicine($medicineId); // fetching medicine details from prescription table

        if ($prescribedMedicine)
            return response()->json(['success' => true, 'prescribedMedicine' => new PrescribedMedicineResource($prescribedMedicine)], 200);
        else
            return response()->json(['success' => false, 'message' => 'not found!!!'], 404);

    }

    /**
     * @param PrescriptionUpdateRequest $request
     * @param MedicineService $medicineService
     * @param PrescriptionService $prescriptionService
     * @return JsonResponse
     */
    public function updatePrescription(PrescriptionUpdateRequest $request, MedicineService $medicineService, PrescriptionService $prescriptionService): JsonResponse
    {
        $validatedData = $request->validated(); // validates data

        DB::beginTransaction();
        try {

            $prescription = $prescriptionService->findById($validatedData['formData']['id']);

            if (!$prescription) throw new \Exception('invalid request'); // checks if prescription id is valid

            $medicine = $medicineService->findByName($validatedData['formData']['medicine']['name']); // checks medicine name already exist in DB

            if (!$medicine)
                $medicine = $medicineService->create($validatedData['formData']['medicine']['name']); // add medicine into DB if not exist

            $prescriptionService->updatePrescription($prescription, $medicine->id, $validatedData['formData']); // updating prescription info

            $prescription->dose()->delete(); // deletes all existing dosage time

            $this->addDosage($prescription, $validatedData['formData']['dose']);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'prescription has been successfully updated',], 201);
        } catch (\Exception $error) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => 'update failed' . $error,], 500);
        }

    }

    /**
     * @param $medicineId
     * @param PrescriptionService $prescriptionService
     * @return JsonResponse
     */
    public function dosageDetails($medicineId, PrescriptionService $prescriptionService): JsonResponse
    {
        $dosageDetails = $prescriptionService->dosageDetails($medicineId);

        if ($dosageDetails) {
            return response()->json([
                'success' => true,
                'dosageDetails' => $dosageDetails,
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'not found',
            ], 404);
        }
    }
}
