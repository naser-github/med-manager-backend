<?php

namespace App\Http\Traits;

use App\Models\Medicine;

trait HelperFunctionTrait
{

    /**
     * add dosage time of a medicine in the database
     * @param $medicine
     * @param $doseDetails
     * @return void
     */
    public function addDosage($medicine, $doseDetails, $medicineStatus = 'active', $flag = 0): void
    {
        foreach ($doseDetails as $dose) {
            $medicine->dose()->create([
                'label' => $dose['label'],
                'time' => $dose['time'],
                'status' => $flag === 0 ? (array_key_exists('status', $dose) ? $dose['status'] : 'active') : $medicineStatus,
            ]);
        }
    }
}
