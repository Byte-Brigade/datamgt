<?php

namespace App\Http\Controllers;

use App\Imports\SkBirtgsImport;
use App\Models\OpsSkbirtgs;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Validators\ValidationException;

class OpsSkbirtgsController extends Controller
{
    public function index()
    {
        $skbirtgsProps = OpsSkbirtgs::with(['branches', 'penerima_kuasa'])->paginate(10);

        return Inertia::render('Ops/SKBIRTGS/Page', [
            'sks' => $skbirtgsProps,
        ]);
    }

    public function import(Request $request)
    {
        try {
            (new SkBirtgsImport)->import($request->file('file')->store('temp'));

            return redirect('ops.skbirtgs')->with(['status' => 'success', 'message' => 'Import Success']);
        } catch (ValidationException $e) {
            $failures = $e->failures();

            foreach ($failures as $failure) {
                $failure->row(); // row that went wrong
                $failure->attribute(); // either heading key (if using heading row concern) or column index
                $failure->errors(); // Actual error messages from Laravel validator
                $failure->values(); // The values of the row that has failed.
            }
            dd($failures);
            return redirect('ops.skbirtgs')->with(['status' => 'failed', 'message' => 'Import Failed']);
        }
    }
}
