<?php

namespace App\Http\Controllers;

use App\Http\Traits\HelperFunctionTrait;
use App\Models\Prescription;
use App\Models\User;
use Carbon\Carbon;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TestController extends Controller
{
    use HelperFunctionTrait;

    public function test()
    {
//        $prescriptionExist = Prescription::where('fk_user_id', 2)
//            ->leftJoin('medicines', 'medicines.id', '=', 'prescriptions.fk_medicine_id')
//            ->where('medicines.name', 'napa')
//            ->where('time_period', '>=', Carbon::today())
//            ->first();
//
//        if ($prescriptionExist && $prescriptionExist->status == 'inactive')
//            $prescriptionNotSaved[] = 'napa';
//
//        dd($prescriptionExist, $prescriptionNotSaved);

//        else {
//            $prescription = new Prescription();
//            $prescription->fk_user_id = AuthService.php::id();
//            $prescription->fk_medicine_id = $medicine->id;
//            $prescription->time_period = $timePeriod;
//            $prescription->save();
//
//            foreach ($data['doseDetails'] as $dose) {
//                $prescription->dose()->create([
//                    'label' => $dose['label'],
//                    'time' => $dose['time'],
//                ]);
//            }
//        }
    }
}
