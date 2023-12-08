<?php

namespace App\Http\Controllers;

use App\Imports\BroImport;
use App\Models\Branch;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Throwable;

class InfraBroController extends Controller
{
    public function index()
    {
        $branches = Branch::get();
        return Inertia::render('GA/Infra/BRO/Page', ['branches' => $branches]);
    }

    public function import(Request $request)
    {
        try {
            (new BroImport)->import($request->file('file'));

            return redirect(route('infra.bros'))->with(['status' => 'success', 'message' => 'Import Berhasil']);
        } catch (Throwable $e) {
            dd($e);
            return redirect(route('infra.bros'))->with(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }
}
