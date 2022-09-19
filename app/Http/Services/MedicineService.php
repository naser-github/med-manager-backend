<?php

namespace App\Http\Services;

use App\Models\Medicine;

class MedicineService
{
    public function create($payload){
        $medicine = new Medicine();
        $medicine->name = $payload;
        $medicine->save();

        return $medicine;
    }

    public function findByName($payload){
        return Medicine::where('name', $payload)->first();
    }

    public function searchByName($payload){
        return Medicine::query()->where('name', 'like', '%' . $payload . '%')->limit(3)->get();
    }
}
