<?php

namespace App\Jobs;

use App\Mail\ReminderMail;
use App\Models\ReminderHistory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class ReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $reminder;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($reminder)
    {
        $this->reminder = $reminder;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        ReminderHistory::query()->create([
            'fk_user_id' => $this->reminder->userId,
            'number_of_medicines' => $this->reminder->numberOfMedicine
        ]);
        Mail::to($this->reminder->email)->send(new ReminderMail($this->reminder));
    }
}
