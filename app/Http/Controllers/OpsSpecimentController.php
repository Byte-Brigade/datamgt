<?php

namespace App\Http\Controllers;
use App\Exports\SpecimentsExport;
use App\Http\Resources\SpecimentResource;
use App\Imports\SpecimentImport;
use App\Models\Branch;
use App\Models\OpsSpeciment;
use FFI\Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Maatwebsite\Excel\Validators\ValidationException;

class OpsSpecimentController extends Controller
{


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


    public function template()
    {
        $path = 'app\public\templates\template_speciment.xlsx';

        return response()->download(storage_path($path));
    }


    public function import(Request $request)
    {
        try {
            (new SpecimentImport)->import($request->file('file'));


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
