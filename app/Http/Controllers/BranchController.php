<?php

namespace App\Http\Controllers;

use Exception;
use Inertia\Inertia;
use App\Models\Branch;
use App\Models\BranchType;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Redis;
use App\Exports\BranchesExport;
use App\Imports\BranchesImport;
use App\Http\Resources\BranchResource;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Validators\ValidationException;

class BranchController extends Controller
{

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
        $areas = Branch::distinct()->whereNotNull('area')->pluck('area');

        return Inertia::render('Ops/Cabang/Page', [
            'branches' => Branch::get(),
            'branch_types' => BranchType::get(),
            'areas' => $areas
        ]);
    }

    public function import(Request $request)
    {
        try {
            (new BranchesImport)->import($request->file('file'));


            return Redirect::back()->with(['status' => 'success', 'message' => 'Import Berhasil']);
        } catch (ValidationException $e) {
            $errorString = '';
            /** @var array $messages */
            foreach ($e->errors() as $field => $messages) {
                foreach ($messages as $message) {
                    $errorString .= "Field {$field}: {$message} ";
                }
            }
            $errorString = trim($errorString);

            return Redirect::back()->with(['status' => 'failed', 'message' => $errorString]);
        } catch (\Throwable $th) {
            return Redirect::back()->with(['status' => 'failed', 'message' => $th->getMessage()]);
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
            $branch = Branch::create([
                'branch_type_id' => $request->branch_type_id,
                'branch_code' => $request->branch_code,
                'branch_name' => $request->branch_name,
                'address' => $request->address,
                'telp' => $request->telp,
                'layanan_atm' => $request->layanan_atm,
                'npwp' => $request->npwp,
                'area' => $request->area,
            ]);

            $branch_type = strtolower($branch->branch_types->type_name);
            $branch_name = strtolower($request->branch_name);
            if ($branch_type == 'kfno' || $branch_type == 'kfo') {
                $branch_type = 'kf';
            }

            $slug = Str::slug($branch_type . " " . $branch_name, '-');
            $branch->slug = $slug;
            $branch->save();

            if (!is_null($request->file('file_ojk'))) {
                $file = $request->file('file_ojk');
                $fileName = $file->getClientOriginalName();
                $file->storeAs("ops/branches/{$branch->id}/", $fileName, ["disk" => "public"]);

                $branch->file_ojk = $fileName;
                $branch->save();
            }

            return redirect(route('ops.branches'))->with(['status' => 'success', 'message' => 'Data berhasil ditambah']);
        } catch (Exception $e) {
            return redirect(route('ops.branches'))->with(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }
    public function upload(Request $request, $id)
    {
        try {
            $branch = Branch::find($id);
            $file = $request->file('file_ojk');

            $fileName = $file->getClientOriginalName();
            $file->storeAs("ops/branches/{$branch->id}/", $fileName, ["disk" => 'public']);

            $branch->file_ojk = $fileName;
            $branch->save();

            return redirect(route('ops.branches'))->with(['status' => 'success', 'message' => 'File berhasil diupload!']);
        } catch (Exception $e) {
            dd($e);

            return redirect(route('ops.branches'))->with(['status' => 'failed', 'message' => 'File gagal diupload!']);
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

            if ($branch->isDirty(['branch_code', 'branch_name'])) {
                $branch_type = strtolower($branch->branch_types->type_name);
                $branch_name = strtolower($request->branch_name);

                if ($branch_type == 'kfno' || $branch_type == 'kfo') {
                    $branch_type = 'kf';
                }

                $slug = Str::slug($branch_type . " " . $branch_name, '-');
                $branch->slug = $slug;
                $branch->save();
            }

            if (!is_null($request->file('file_ojk'))) {
                $file = $request->file('file_ojk');
                $fileName = $file->getClientOriginalName();
                $file->storeAs("ops/branches/{$branch->id}/", $fileName, ["disk" => "public"]);

                $branch->file_ojk = $fileName;
                $branch->save();
            }

            return redirect(route('ops.branches'))->with(['status' => 'success', 'message' => 'Data berhasil diubah']);
        } catch (Exception $e) {
            return redirect(route('ops.branches'))->with(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        $branch = Branch::find($id);
        Storage::disk('public')->delete('ops/branches/' . $branch->id . '/');
        $branch->delete();

        return redirect(route('ops.branches'))->with(['status' => 'success', 'message' => 'Data berhasil dihapus']);
    }
}
