<?php

namespace App\Http\Traits;

use App\Models\Medicine;

trait HelperFunctionTrait
{

    public function medicineExist($name)
    {
        return Medicine::where('name', $name)->first();
    }

    public function addDosage($medicine, $doseDetails)
    {
        foreach ($doseDetails as $dose) {
            $medicine->dose()->create([
                'label' => $dose['label'],
                'time' => $dose['time'],
            ]);
        }
    }
}
