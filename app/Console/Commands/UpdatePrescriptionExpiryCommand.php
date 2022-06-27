<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdatePrescriptionExpiryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:prescription_expiry';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $expired = DB::table('prescriptions')
            ->select(
                'prescriptions.fk_user_id', 'prescriptions.fk_medicine_id', 'prescriptions.time_period', 'prescriptions.status as medicineStatus',
                'dosages.label', 'dosages.time', 'dosages.status as doseStatus'
            )
            ->leftJoin('dosages', 'dosages.dosage_id', '=', 'prescriptions.id')
            ->where('prescriptions.status', 'active')
            ->where('prescriptions.time_period', '<', Carbon::today())
            ->update(['prescriptions.status' => 'inactive', 'dosages.status' => 'inactive',]);

    }
}
