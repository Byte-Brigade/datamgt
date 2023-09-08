<?php

namespace App\Http\Controllers;

use App\Http\Resources\PajakReklameResource;
use App\Imports\PajakReklameImport;
use App\Models\OpsPajakReklame;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Validators\ValidationException;

class OpsPajakReklameController extends Controller
{
    protected array $sortFields = ['periode_awal', 'periode_akhir'];

    public function __construct(public OpsPajakReklame $ops_pajak_reklame)
    {
    }

    public function api(Request $request)
    {
        $sortFieldInput = $request->input('sort_field', 'id');
        $sortField = in_array($sortFieldInput, $this->sortFields) ? $sortFieldInput : 'id';
        $sortOrder = $request->input('sort_order', 'asc');
        $searchInput = $request->search;
        $query = $this->ops_pajak_reklame->orderBy($sortField, $sortOrder);
        $perpage = $request->perpage ?? 10;

        if (!is_null($searchInput)) {
            $searchQuery = "%$searchInput%";
            $query = $query->where('periode_awal', 'like', $searchQuery)->orWhere('periode_akhir', 'like', $searchQuery);
        }
        $employees = $query->paginate($perpage);
        return PajakReklameResource::collection($employees);
    }

    public function index(Request $request)
    {
        return Inertia::render('Ops/PajakReklame/Page');
    }

    public function import(Request $request)
    {
        try {
            (new PajakReklameImport)->import($request->file('file')->store('temp'));

            return redirect(route('ops.pajak-reklame'))->with(['status' => 'success', 'message' => 'Import Success']);
        } catch (ValidationException $e) {
            $failures = $e->failures();

            foreach ($failures as $failure) {
                $failure->row(); // row that went wrong
                $failure->attribute(); // either heading key (if using heading row concern) or column index
                $failure->errors(); // Actual error messages from Laravel validator
                $failure->values(); // The values of the row that has failed.
            }
            dd($failures);
            return redirect(route('ops.pajak-reklame'))->with(['status' => 'failed', 'message' => 'Import Failed']);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $pajak_reklame = OpsPajakReklame::find($id);
            $pajak_reklame->update([
                'periode_awal' => $request->periode_awal,
                'periode_akhir' => $request->periode_akhir,
                'note' => $request->note,
                'additional_info' => $request->additional_info,
            ]);

            return redirect(route('ops.pajak-reklame'))->with(['status' => 'success', 'message' => 'Data berhasil diubah']);
        } catch (\Exception $e) {
            return redirect(route('ops.pajak-reklame'))->with(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        $pajak_reklame = OpsPajakReklame::find($id);
        $pajak_reklame->delete();

        return redirect(route('ops.pajak-reklame'))->with(['status' => 'success', 'message' => 'Data berhasil dihapus']);
    }
}
