<?php

namespace App\Http\Services;

use App\Models\Medicine;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class MedicineService
{
    /**
     * @param $payload
     * @return Medicine
     */
    public function create($payload): Medicine
    {
        $medicine = new Medicine();
        $medicine->name = $payload;
        $medicine->save();

        return $medicine;
    }


    /**
     * @param $payload
     * @return Model|Builder|null
     */
    public function findByName($payload): Model|Builder|null
    {
        return Medicine::query()->where('name', $payload)->first();
    }

    /**
     * @param $payload
     * @return Collection|array
     */
    public function searchByName($payload): Collection|array
    {
        return Medicine::query()->where('name', 'like', '%' . $payload . '%')->limit(3)->get();
    }
}
