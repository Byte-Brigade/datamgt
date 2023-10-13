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


        $input = $request->all();
        if (isset($input['branch_types_type_name'])) {
            $type_name =$input['branch_types_type_name'];
            $query = $query->whereHas('branch_types', function (Builder $q) use ($type_name) {
                if(in_array('KF',$type_name)) {
                    return $q->whereIn('type_name',['KF','KFNO']);
                }
                return $q->whereIn('type_name', $type_name);
            });
        }

        if (isset($request->layanan_atm)) {
            $query = $query->whereIn('layanan_atm', $request->layanan_atm);
        }

        if (!is_null($searchInput)) {
            $searchQuery = "%$searchInput%";
            $query = $query->where(function($query) use($searchQuery) {
                $query->where('branch_code', 'like', $searchQuery)
                ->orWhere('branch_name', 'like', $searchQuery)
                ->orWhere('address', 'like', $searchQuery);
            });
        }


        $branches = $query->paginate($perpage);

        return BranchResource::collection($branches);
    }

    private function handleColumn($column)
    {
        if (str_contains($column, '.')) {
            $arr = explode('.', $column);
            $column = array_shift($arr);
            $column = $arr[0];
        }
        return $column;
    }

    public function index(Request $request)
    {

        return Inertia::render('Cabang/Page', [
            'branches' => Branch::get(),
            'branch_types' => BranchType::get(),
        ]);
    }

    public function import(Request $request)
    {
        try {
            (new BranchesImport)->import($request->file('file')->store('temp'));


            return redirect(route('branches'))->with(['status' => 'success', 'message' => 'Import Berhasil']);
        } catch (ValidationException $e) {
            $failures = $e->failures();

            foreach ($failures as $failure) {
                $failure->row(); // row that went wrong
                $failure->attribute(); // either heading key (if using heading row concern) or column index
                $failure->errors(); // Actual error messages from Laravel validator
                $failure->values(); // The values of the row that has failed.
            }
            dd($failures);

            return redirect(route('branches'))->with(['status' => 'failed', 'message' => 'Import Gagal']);
        }
    }

    public function export()
    {
        $fileName = 'Data_Cabang_' . date('d-m-y') . '.xlsx';
        return (new BranchesExport)->download($fileName);
    }

    public function store(Request $request)
    {
        try {

            Branch::create([
                'branch_type_id' => $request->branch_type_id,
                'branch_code' => $request->branch_code,
                'branch_name' => $request->branch_name,
                'address' => $request->address,
                'telp' => $request->telp,
                'layanan_atm' => $request->layanan_atm,
                'npwp' => $request->npwp
            ]);

            return redirect(route('branches'))->with(['status' => 'success', 'message' => 'Data berhasil diubah']);
        } catch (\Exception $e) {
            return redirect(route('branches'))->with(['status' => 'failed', 'message' => $e->getMessage()]);
        }
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

            return redirect(route('branches'))->with(['status' => 'success', 'message' => 'Data berhasil diubah']);
        } catch (\Exception $e) {
            return redirect(route('branches'))->with(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        $branch = Branch::find($id);
        $branch->delete();

        return redirect(route('branches'))->with(['status' => 'success', 'message' => 'Data berhasil dihapus']);
    }
}
