<?php

namespace App\Http\Services;

use App\Models\Medicine;

class MedicineService
{
    public function create($payload): object
    {
        $medicine = new Medicine();
        $medicine->name = $payload;
        $medicine->save();

        return $medicine;
    }

    public function findByName($payload)
    {
        return Medicine::where('name', $payload)->first();
    }
}
