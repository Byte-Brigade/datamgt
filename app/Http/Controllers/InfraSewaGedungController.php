<?php

namespace App\Http\Controllers;

use App\Http\Resources\SewaGedungResource;
use App\Imports\SewaGedungImport;
use App\Models\Branch;
use App\Models\InfraSewaGedung;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Throwable;

class InfraSewaGedungController extends Controller
{
    public function index()
    {
        $branches = Branch::get();
        return Inertia::render('GA/Infra/SewaGedung/Page', ['branches' => $branches]);
    }

    public function import(Request $request)
    {
        try {
            (new SewaGedungImport)->import($request->file('file'));

            return redirect(route('infra.sewa_gedungs'))->with(['status' => 'success', 'message' => 'Import Berhasil']);
        } catch (Throwable $e) {
            dd($e);
            return redirect(route('infra.sewa_gedungs'))->with(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }
}

