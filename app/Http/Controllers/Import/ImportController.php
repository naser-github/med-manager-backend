<?php

namespace App\Http\Controllers\Import;

use App\Http\Controllers\Controller;
use App\Jobs\Import\MedicineDataImportJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImportController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function importMedicineData(Request $request)
    {
//        $tmp_path = public_path('medicine_temp/' . Auth::id().'/');
////        return $tmp_path;
//        return ['f'=>file_exists($tmp_path)];
//
//        if (!is_dir($tmp_path)) rmdir($tmp_path);

        $request->validate([
            'file' => 'required|mimes:csv',
        ]);

        $imported_file = $request->file('file');
        $file_name = Auth::id() . '_' . $imported_file->getClientOriginalName();
        $imported_file->move(public_path('/upload'), $file_name);

        $path = public_path('upload') . '/' . $file_name;
        if (!$path) return response()->json(['success' => false, 'message' => 'No file found!!!'], 404);

        $importing_data = file($path);
        unset($importing_data[0]); // removing first row [header of the file] from the file

        $chunks = array_chunk($importing_data, 5000); // Chunking files with 5000 rows each
        unlink($path);

        // Convert 5000 record in one csv
        if ($chunks) {
            $tmp_path = public_path('medicine_temp/' . Auth::id());
            if (!is_dir($tmp_path)) mkdir($tmp_path, 0777, true);

            foreach ($chunks as $key => $chunk) {
                $name = "/tmp{$key}.csv";
                file_put_contents($tmp_path . $name, $chunk);
            }
            $this->chunkInsert();

            return response()->json(['success' => true, 'message' => 'import successfully done'], 201);
        }
        return response()->json(['success' => false, 'message' => 'something is wrong!!!'], 404);

    }


    /**
     * @return void
     */
    public function chunkInsert(): void
    {
        $path = public_path('medicine_temp/' . Auth::id());
        $files = glob("$path/*.csv");

        if ($files) {
            foreach ($files as $file) {
                MedicineDataImportJob::dispatch($file, $path);
            }
        }
    }
}
