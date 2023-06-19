<?php

namespace App\Jobs\Import;

use App\Models\Medicine;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;

class MedicineDataImportJob implements ShouldQueue, SkipsEmptyRows, SkipsOnError, SkipsOnFailure
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, SkipsErrors, SkipsFailures;

    public $file;
    public $temp_path;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($file, $temp_path)
    {
        $this->file = $file;
        $this->temp_path = $temp_path;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $data = array_map('str_getcsv', file($this->file));

        foreach ($data as $key => $col) {
            try {
                if ($col[1]) {
                    $medicine = Medicine::query()->where('name', $col[1])->first();

                    if (!$medicine) $medicine = new Medicine();

                    $medicine->name = $col[1];
                    $medicine->save();
                }
            } catch (\Exception $exception) {
                Log::alert($exception);
            }
        }
        unlink($this->file);

        $files = glob("$this->temp_path/*");
        if (!$files) rmdir($this->temp_path);
    }
}
