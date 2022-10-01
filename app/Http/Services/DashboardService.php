<?php

namespace App\Http\Services;

use App\Models\Medicine;
use App\Models\Prescription;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardService
{
    public function dailyDoseList()
    {
        return Prescription::query()
            ->where('prescriptions.fk_user_id', Auth::id())
            ->where('prescriptions.time_period', '>=', Carbon::now()->format('Y-m-d'))
            ->where('prescriptions.status', 'active')
            ->leftJoin('medicines', 'medicines.id', '=', 'prescriptions.fk_medicine_id')
            ->leftJoin('dosages', 'dosages.dosage_id', '=', 'prescriptions.id')
            ->where('dosages.status', 'active',)
            ->selectRaw('
                group_concat(medicines.name) as medicine_name,
                dosages.label as label,
                dosages.time as dose_time,
                count(*) as count
            ')
            ->groupBy('dose_time')
            ->orderBy('dose_time', 'ASC')
            ->get();
    }
}
