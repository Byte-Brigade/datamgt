<?php

namespace App\Http\Controllers;

use App\Exports\PajakReklameExport;
use App\Http\Resources\PajakReklameResource;
use App\Imports\PajakReklameImport;
use App\Models\Branch;
use App\Models\OpsPajakReklame;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Validators\ValidationException;

class OpsPajakReklameController extends Controller
{
    protected array $sortFields = ['ops_pajak_reklames.id', 'branches.branch_code', 'branches.branch_name', 'periode_awal', 'periode_akhir'];

    public function __construct(public OpsPajakReklame $ops_pajak_reklame)
    {
    }

    public function api(Request $request)
    {
        $sortFieldInput = $request->input('sort_field', 'ops_pajak_reklames.id');
        $sortField = in_array($sortFieldInput, $this->sortFields) ? $sortFieldInput : 'ops_pajak_reklames.id';
        $sortOrder = $request->input('sort_order', 'asc');
        $searchInput = $request->search;
        $query = $this->ops_pajak_reklame->select('ops_pajak_reklames.*')->orderBy($sortField, $sortOrder)
            ->join('branches', 'ops_pajak_reklames.branch_id', 'branches.id');
        $perpage = $request->perpage ?? 10;

        if (!is_null($searchInput)) {
            $searchQuery = "%$searchInput%";
            $query = $query->where('periode_awal', 'like', $searchQuery)
                ->orWhere('periode_akhir', 'like', $searchQuery)
                ->orWhere('branch_code', 'like', $searchQuery)
                ->orWhere('branch_name', 'like', $searchQuery);
        }
        $employees = $query->paginate($perpage);
        return PajakReklameResource::collection($employees);
    }

    public function index(Request $request)
    {
        $branchesProps = Branch::get();
        return Inertia::render('Ops/PajakReklame/Page', ['branches' => $branchesProps]);
    }

    public function import(Request $request)
    {
        try {
            (new PajakReklameImport)->import($request->file('file')->store('temp'));

            return redirect(route('ops.pajak-reklame'))->with(['status' => 'berhasil', 'message' => 'Import Success']);
        } catch (ValidationException $e) {
            $failures = $e->failures();

            foreach ($failures as $failure) {
                $failure->row(); // row that went wrong
                $failure->attribute(); // either heading key (if using heading row concern) or column index
                $failure->errors(); // Actual error messages from Laravel validator
                $failure->values(); // The values of the row that has failed.
            }
            dd($failures);
            return redirect(route('ops.pajak-reklame'))->with(['status' => 'gagal', 'message' => 'Import Failed']);
        }
    }

    public function export(Request $request)
    {
        $fileName = 'Data_Pajak_Reklame_' . date('d-m-y') . '.xlsx';

        return (new PajakReklameExport($request->branch))->download($fileName);
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

            return redirect(route('ops.pajak-reklame'))->with(['status' => 'berhasil', 'message' => 'Data berhasil diubah']);
        } catch (\Exception $e) {
            return redirect(route('ops.pajak-reklame'))->with(['status' => 'gagal', 'message' => $e->getMessage()]);
        }
    }

    public function upload(Request $request, $id)
    {
        try {
            $ops_pajak_reklame = OpsPajakReklame::find($id);
            $file = $request->file('file_izin_reklame');

            if(!is_null($request->file('file_skpd'))) {
                $file = $request->file('file_skpd');
            }

            $fileName = $file->getClientOriginalName();
            $file->storeAs('ops/pajak-reklame/', $fileName, ["disk" => 'public']);

            if(!is_null($request->file('file_skpd'))) {
                $ops_pajak_reklame->file_skpd = $fileName;
            } else {
                $ops_pajak_reklame->file_izin_reklame = $fileName;
            }

            $ops_pajak_reklame->save();

            return redirect(route('ops.pajak-reklame'))->with(['status' => 'berhasil', 'message' => 'File berhasil diupload!']);
        } catch (Exception $e) {
            dd($e);

            return redirect(route('ops.pajak-reklame'))->with(['status' => 'gagal', 'message' => 'File gagal diupload!']);
        }
    }


    public function destroy($id)
    {
        $pajak_reklame = OpsPajakReklame::find($id);
        $pajak_reklame->delete();

        return redirect(route('ops.pajak-reklame'))->with(['status' => 'berhasil', 'message' => 'Data berhasil dihapus']);
    }
}
