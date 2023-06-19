<?php

namespace App\Http\Traits;

use App\Models\Medicine;

trait HelperFunctionTrait
{
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

    public function passwordGenerator($length): string
    {
        $data = '1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZabcefghijklmnopqrstuvwxyz';
        return substr(str_shuffle($data), 0, $length);
    }
}
