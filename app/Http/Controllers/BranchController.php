<?php

namespace App\Http\Controllers;

use App\Exports\BranchesExport;
use App\Http\Resources\BranchResource;
use App\Imports\BranchesImport;
use App\Models\Branch;
use App\Models\BranchType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Validators\ValidationException;

class BranchController extends Controller
{
    protected array $sortFields = ['branch_types.type_name', 'branch_code', 'branch_name', 'address'];

    public function __construct(public Branch $branch)
    {
    }
    public function api(Request $request)
    {
        $sortFieldInput = $request->input('sort_field', 'branch_code');
        $sortField = in_array($sortFieldInput, $this->sortFields) ? $sortFieldInput : 'branch_name';
        $sortOrder = $request->input('sort_order', 'asc');
        $searchInput = $request->search;
        $query = $this->branch->select('branches.*')->orderBy($sortField, $sortOrder)
            ->join('branch_types', 'branches.branch_type_id', 'branch_types.id');
        $perpage = $request->perpage ?? 10;

        if (!is_null($searchInput)) {
            $searchQuery = "%$searchInput%";
            $query = $query->where('branch_code', 'like', $searchQuery)
                ->orWhere('branch_name', 'like', $searchQuery)
                ->orWhere('address', 'like', $searchQuery)
                ->orWhere('layanan_atm', 'like', $searchQuery)
                ->orWhereHas('branch_types', function (Builder $q) use ($searchQuery) {
                    $q->where('type_name', 'like', $searchQuery);
                });
        }
        $branches = $query->paginate($perpage);
        return BranchResource::collection($branches);
    }

    public function index(Request $request)
    {
        return Inertia::render('Cabang/Page', [
            'branch_types' => BranchType::all(),
        ]);
    }

    public function import(Request $request)
    {
        try {
            (new BranchesImport)->import($request->file('file')->store('temp'));

            return redirect(route('branches'))->with(['status' => 'berhasil', 'message' => 'Import data berhasil']);
        } catch (ValidationException $e) {
            $failures = $e->failures();

            foreach ($failures as $failure) {
                $failure->row(); // row that went wrong
                $failure->attribute(); // either heading key (if using heading row concern) or column index
                $failure->errors(); // Actual error messages from Laravel validator
                $failure->values(); // The values of the row that has failed.
            }
            dd($failures);
            return redirect(route('branches'))->with(['status' => 'gagal', 'message' => 'Import data gagal']);
        }

    }

    public function export()
    {
        $fileName = 'Data_Cabang_' . date('d-m-y') . '.xlsx';
        return (new BranchesExport)->download($fileName);
    }

    public function update(Request $request, $id)
    {
        try {
            $layanan_atm = $request->layanan_atm != '' ? $request->layanan_atm : null;
            $branch = Branch::find($id);
            $branch->update([
                'branch_type_id' => $request->branch_type_id,
                'branch_code' => $request->branch_code,
                'branch_name' => $request->branch_name,
                'address' => $request->address,
                'telp' => $request->telp,
                'layanan_atm' => $layanan_atm,
            ]);

            return redirect(route('branches'))->with(['status' => 'berhasil', 'message' => 'Data berhasil diubah']);
        } catch (\Exception $e) {
            return redirect(route('branches'))->with(['status' => 'gagal', 'message' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        $branch = Branch::find($id);
        $branch->delete();

        return redirect(route('branches'))->with(['status' => 'berhasil', 'message' => 'Data berhasil dihapus']);
    }
}
