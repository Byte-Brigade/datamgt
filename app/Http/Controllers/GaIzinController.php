<?php

namespace App\Http\Controllers;

use App\Http\Resources\IzinResource;
use App\Imports\IzinImport;
use App\Models\GaIzin;
use App\Models\JenisPerizinan;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Throwable;

class GaIzinController extends Controller
{

    protected array $sortFields = ['jenis_perizinan.name', 'tgl_pengesahan'];

    public function api(GaIzin $ga_izin, Request $request)
    {
        $sortField = 'id';
        $sortOrder = $request->input('sort_order', 'asc');
        $searchInput = $request->search;
        $query = $ga_izin->orderBy($sortField, $sortOrder);

        $perpage = $request->perpage ?? 10;

        if (!is_null($searchInput)) {
            $searchQuery = "%$searchInput%";
            $query = $query->where('id', 'like', $searchQuery);
        }
        $employees = $query->paginate($perpage);
        return IzinResource::collection($employees);
    }


    public function index()
    {
        $branchesProps = JenisPerizinan::get();
        return Inertia::render('GA/Izin/Page', ['gaizins' => $branchesProps]);
    }

    public function import(Request $request)
    {
        try {
            (new IzinImport)->import($request->file('file')->store('temp'));

            return redirect(route('ga.izin'))->with(['status' => 'success', 'message' => 'Import Berhasil']);
        } catch (Throwable $e) {
            dd($e);
            return redirect(route('ga.izin'))->with(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }


    public function export()
    {

    }
    public function update()
    {

    }

    public function destroy()
    {

    }
}
