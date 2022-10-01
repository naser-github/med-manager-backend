<?php

namespace App\Console\Commands;

use App\Jobs\ReminderJob;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ReminderCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';


    /**
     * @return void
     */
    public function handle(): void
    {
        $reminders = DB::table('prescriptions')
            ->select(
                'users.id as userId','users.name as userName', 'users.email',
                DB::raw('GROUP_CONCAT(medicines.name) as medicineName, count(medicines.name) as numberOfMedicine'),
                'prescriptions.fk_user_id', 'prescriptions.fk_medicine_id', 'prescriptions.time_period', 'prescriptions.status as medicineStatus',
                DB::raw('GROUP_CONCAT(dosages.label)'), DB::raw('GROUP_CONCAT(dosages.time) as dosageTime'), 'dosages.status as doseStatus'
            )
            ->where('prescriptions.status', 'active')
            ->leftJoin('users', 'users.id', '=', 'prescriptions.fk_user_id')
            ->where('users.user_status', 'active')
            ->leftJoin('medicines', 'medicines.id', '=', 'prescriptions.fk_medicine_id')
            ->leftJoin('dosages', 'dosages.dosage_id', '=', 'prescriptions.id')
            ->whereBetween(
                'dosages.time', [Carbon::now()->format('H:i:m'), Carbon::now()->addMinute(30)->format('H:i:m')]
            )
            ->where('dosages.status', 'active')
            ->groupBy('prescriptions.fk_user_id')
            ->get();

        foreach ($reminders as $reminder) {
            ReminderJob::dispatch($reminder);
        }
    }
}
