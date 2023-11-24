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
        $branchesProps = Branch::get();
        return Inertia::render('GA/Infra/SewaGedung/Page', ['branches' => $branchesProps]);
    }


    protected array $sortFields = ['branches.branch_code', 'status_kepemilikan'];

    public function api(InfraSewaGedung $infra_sewa_gedung, Request $request)
    {
        $sortFieldInput = $request->input('sort_field', 'branches.branch_code');
        $sortField = in_array($sortFieldInput, $this->sortFields) ? $sortFieldInput : 'branches.branch_code';
        $sortOrder = $request->input('sort_order', 'asc');
        $searchInput = $request->search;
        $query = $infra_sewa_gedung->orderBy($sortField, $sortOrder)
        ->join('branches', 'infra_sewa_gedungs.branch_id', 'branches.id');

        $perpage = $request->perpage ?? 10;

        if (!is_null($searchInput)) {
            $searchQuery = "%$searchInput%";
            $query = $query->where('id', 'like', $searchQuery);
        }
        $data = $query->paginate($perpage);
        return SewaGedungResource::collection($data);
    }


    public function import(Request $request)
    {
        try {
            (new SewaGedungImport)->import($request->file('file')->store('temp'));

            return redirect(route('infra.sewa_gedungs'))->with(['status' => 'success', 'message' => 'Import Berhasil']);
        } catch (Throwable $e) {
            dd($e);
            return redirect(route('infra.sewa_gedungs'))->with(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }
}

