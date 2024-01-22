<?php

namespace App\Http\Controllers;

use App\Exports\Assets\AssetsExport;
use App\Imports\AssetsImport;
use App\Models\Branch;
use App\Models\File;
use App\Models\GapAsset;
use App\Models\History;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Maatwebsite\Excel\Validators\ValidationException;
use Throwable;

class GapAssetController extends Controller
{
    public function index()
    {
        $branches = Branch::get();

        return Inertia::render('GA/Procurement/Asset/Page', ['branches' => $branches]);
    }

    public function import(Request $request)
    {
        try {
            $tableName = 'Asset';
            $timestamp = Carbon::now()->format('YmdHis');
            $uploadedFile = $request->file('file');
            $originalFilename = $uploadedFile->getClientOriginalName();
            $newFilename = "{$timestamp}_{$originalFilename}";
            (new AssetsImport)->import($uploadedFile);

            $path = $uploadedFile->storeAs("files/{$tableName}", $newFilename, 'local'); // 'local' is the disk name
            File::create([
                'table_name' => $tableName,
                'filename' => $newFilename,
                'path' => $path,
            ]);
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
        } catch (Throwable $th) {
            return Redirect::back()->with(['status' => 'failed', 'message' => $th->getMessage()]);
        }
    }

    public function template()
    {
        $path = 'app\public\templates\template_assets.xlsx';

        return response()->download(storage_path($path));
    }


    public function export(Request $request)
    {
        $fileName = 'Data_Asset_BSS_' . date('d-m-y') . '.xlsx';
        return (new AssetsExport)->download($fileName);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $branch = Branch::find($request->branch_id);

            GapAsset::create([
                'branch_id' => $branch->id,
                'category' => $request->category,
                'asset_number' => $request->asset_number,
                'asset_description' => $request->asset_description,
                'asset_cost' => $request->asset_cost,
                'date_in_place_service' => $request->date_in_place_service,
                'asset_location' => $request->asset_location,
                'major_category' => $request->major_category,
                'minor_category' => $request->minor_category,
                'depre_exp' => $request->depre_exp,
                'net_book_value' => $request->net_book_value,
                'periode' => Carbon::now()->format('Y-m-d'),
            ]);
            return redirect(route('gap.assets'))->with(['status' => 'success', 'message' => 'Data Berhasil disimpan']);
        } catch (Throwable $e) {
            return redirect(route('gap.assets'))->with(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $gap_asset = GapAsset::find($id);

            $branch = Branch::find($request->branch_id);
            $gap_asset->update([
                'branch_id' => $branch->id,
                'category' => $request->category,
                'asset_number' => $request->asset_number,
                'asset_description' => $request->asset_description,
                'asset_cost' => $request->asset_cost,
                'date_in_place_service' => $request->date_in_place_service,
                'asset_location' => $request->asset_location,
                'major_category' => $request->major_category,
                'minor_category' => $request->minor_category,
                'depre_exp' => $request->depre_exp,
                'net_book_value' => $request->net_book_value,
            ]);
            return redirect(route('gap.assets'))->with(['status' => 'success', 'message' => 'Data Berhasil diupdate']);
        } catch (Throwable $e) {
            return redirect(route('gap.assets'))->with(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $gap_asset = GapAsset::find($id);
            $gap_asset->delete();
            return redirect(route('gap.assets', $id))->with(['status' => 'success', 'message' => 'Data berhasil dihapus']);
        } catch (Throwable $e) {
            return redirect(route('gap.assets'))->with(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }
}
