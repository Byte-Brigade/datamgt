<?php

namespace App\Http\Controllers;
use App\Exports\SpecimentsExport;
use App\Http\Resources\SpecimentResource;
use App\Imports\SpecimentImport;
use App\Models\Branch;
use App\Models\OpsSpeciment;
use FFI\Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Validators\ValidationException;

class OpsSpecimentController extends Controller
{

    public function __construct(public OpsSpeciment $ops_speciment)
    {
    }
    protected array $sortFields = ['branches.branch_code'];

    public function api(Request $request)
    {
        $sortFieldInput = $request->input('sort_field', 'branches.branch_code');
        $sortField = in_array($sortFieldInput, $this->sortFields) ? $sortFieldInput : 'ops_speciments.id';
        $sortOrder = $request->input('sort_order', 'asc');
        $searchInput = $request->search;
        $query = $this->ops_speciment->select('ops_speciments.*')->orderBy($sortField, $sortOrder)
        ->join('branches', 'ops_speciments.branch_id', 'branches.id');
        $perpage = $request->perpage ?? 10;

        if (!is_null($searchInput)) {
            $searchQuery = "%$searchInput%";
            $query = $query->where('tgl_speciment', 'like', $searchQuery);
        }
        $employees = $query->paginate($perpage);
        return SpecimentResource::collection($employees);
    }

    public function index(Request $request)
    {
        $branchesProp = Branch::get();
        return Inertia::render('Ops/Speciment/Page', ['branches' => $branchesProp]);
    }

    public function upload(Request $request, $id)
    {
        try {
            $ops_speciment = OpsSpeciment::find($id);

            $fileName = $request->file('file')->getClientOriginalName();
            $request->file('file')->storeAs('ops/speciment/', $fileName, ["disk" => 'public']);

            $ops_speciment->file = $fileName;
            $ops_speciment->save();

            return redirect(route('ops.speciment'))->with(['status' => 'success', 'message' => 'File berhasil diupload!']);
        } catch (Exception $e) {
            dd($e);

            return redirect(route('ops.speciment'))->with(['status' => 'failed', 'message' => 'File gagal diupload!']);
        }
    }

    public function import(Request $request)
    {
        try {
            (new SpecimentImport)->import($request->file('file')->store('temp'));

            return redirect(route('ops.speciment'))->with(['status' => 'success', 'message' => 'Import Berhasil']);
        } catch (ValidationException $e) {
            $failures = $e->failures();

            foreach ($failures as $failure) {
                $failure->row(); // row that went wrong
                $failure->attribute(); // either heading key (if using heading row concern) or column index
                $failure->errors(); // Actual error messages from Laravel validator
                $failure->values(); // The values of the row that has failed.
            }
            dd($failures);
            return redirect(route('ops.speciment'))->with(['status' => 'failed', 'message' => 'Import Gagal']);
        }
    }

    public function export()
    {
        $fileName = 'Data_Speciment_' . date('d-m-y') . '.xlsx';
        return (new SpecimentsExport)->download($fileName);
    }

    public function store(Request $request)
    {
        try {
            $speciment = OpsSpeciment::create([
                'branch_id' => $request->branch_id,
                'tgl_speciment' => $request->tgl_speciment,

            ]);

            return redirect(route('ops.speciment'))->with(['status' => 'success', 'message' => 'Data berhasil ditambahkan']);
        } catch (\Exception $e) {
            return redirect(route('ops.speciment'))->with(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }
    public function update(Request $request, $id)
    {
        try {
            $speciment = OpsSpeciment::find($id);
            $speciment->update([
                'tgl_speciment' => $request->tgl_speciment,

            ]);

            return redirect(route('ops.speciment'))->with(['status' => 'success', 'message' => 'Data berhasil diubah']);
        } catch (\Exception $e) {
            return redirect(route('ops.speciment'))->with(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        $speciment = OpsSpeciment::find($id);
        $speciment->delete();

        return redirect(route('ops.speciment'))->with(['status' => 'success', 'message' => 'Data berhasil dihapus']);
    }
}
