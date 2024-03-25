<?php

namespace App\Http\Controllers;

use App\Exports\HasilSTO\HasilSTOExport;
use App\Exports\STO\STOExport;
use App\Models\Branch;
use App\Models\GapAsset;
use App\Models\GapAssetDetail;
use App\Models\GapHasilSto;
use App\Models\GapSto;
use App\Models\User;
use Carbon\Carbon;
use Error;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use ZipArchive;

class GapStoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inertia::render('GA/Procurement/STO/Page');
    }
    public function detail($gap_sto_id)
    {
        $gap_sto = GapSto::find($gap_sto_id);
        return Inertia::render('GA/Procurement/STO/Detail', ['gap_sto_id' => $gap_sto_id, 'periode' => Carbon::parse($gap_sto->periode)->year, 'semester' => $gap_sto->semester]);
    }
    public function assets(Request $request, $gap_hasil_sto_id)
    {

        $branch = Branch::where('slug', $request->branch)->first();

        $gap_hasil_sto = GapHasilSto::find($gap_hasil_sto_id);
        return Inertia::render('GA/Procurement/STO/STO', ['gap_hasil_sto_id' => $gap_hasil_sto->id, 'branch' => $branch]);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function status(Request $request, $id)
    {
        try {
            $gap_sto = GapSto::find($id);


            $not_submit_disclamer = GapHasilSto::where('gap_sto_id', $gap_sto->id)->whereNull('disclaimer')->count();

            $current_asset = GapAssetDetail::where('periode', $gap_sto->periode)->distinct()->get()->count();
            $assets = GapAsset::count();
            $lastPeriode = GapSto::where('status', 'Done')->max('periode');
            $prev_asset = GapAssetDetail::where('periode', $lastPeriode)->count();
            if ($prev_asset == 0) {
                $prev_asset = $assets;
            }

            if ($not_submit_disclamer > 0) {
                throw new Exception("Terdapat " . $not_submit_disclamer . " Cabang yang belum submit disclaimer");
            }

            if ($current_asset == $prev_asset) {
                $gap_sto->update([
                    'status' => 'Done'
                ]);

                GapAssetDetail::where('periode', $gap_sto->periode)->where('semester', $gap_sto->semester)->update(['sto' => true]);
            } else {
                $count = abs($current_asset - $prev_asset);
                if ($count == 0) {
                    throw new Exception('Belum ada asset');
                }
                throw new Exception((abs($current_asset - $prev_asset) . ' Assets belum diremark'));
            }
            return Redirect::back()->with(['status' => 'success', 'message' => 'STO telah selesai!']);
        } catch (Exception $e) {
            return Redirect::back()->with(['status' => 'failed', 'message' => 'Error: ' . $e->getMessage()]);
        }
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
            $active_sto = GapSto::where('status', 'On Progress')->first();
            if (isset($active_sto)) {
                throw new Exception("Selesaikan STO sebelumnya terlebih dahulu.");
            }

            $exist_sto = GapSto::where('periode', Carbon::parse($request->periode)->startOfMonth()->format('Y-m-d'))->where('semester', $request->semester)->first();
            if (isset($exist_sto)) {
                throw new Exception("STO sudah ada!");
            }

            $gap_sto = GapSto::create([
                'periode' => Carbon::createFromDate($request->periode)->format('Y-m-d'),
                'semester' => $request->semester,
                'status' => 'On Progress',
                'keterangan' => $request->keterangan,
            ]);

            $branches = Branch::whereHas('gap_assets')->get();

            foreach ($branches as $branch) {
                GapHasilSto::create(
                    [
                        'branch_id' => $branch->id,
                        'gap_sto_id' => $gap_sto->id,
                        'remarked' => false,
                    ]
                );
            }
            $role = Role::where('name', 'cabang')->first();
            $permission = Permission::where('name', 'can sto')->first();

            $userIdsWithRole = $role->users()->pluck('id');

            User::whereIn('id', $userIdsWithRole)->each(function ($user) use ($permission) {
                $user->givePermissionTo($permission);
            });
            return Redirect::back()->with(['status' => 'success', 'message' => 'STO berhasil dibuat!']);
        } catch (Exception $e) {
            return Redirect::back()->with(['status' => 'failed', 'message' => 'Data gagal disimpan! ' . $e->getMessage()]);
        }
    }

    public function store_hasil_sto(Request $request, $slug)
    {
        try {
            $branch = Branch::with('gap_assets')->where('slug', $slug)->first();


            $sto = GapSto::where('status', 'On Progress')->first();

            $fileName = $branch->branch_types->type_name.'_'.$branch->branch_name.'_STO_'.Carbon::parse($sto->periode)->year.'_'.$sto->semester;



            if (isset($sto)) {

                $current_asset = $branch->gap_assets()->whereHas('gap_asset_details', function ($q) use ($sto) {
                    return $q->where('periode', $sto->periode)->where('semester', $sto->semester);
                })->count();
                $assets = GapAsset::where('branch_id', $branch->id)->count();
                $prevSTO = GapSto::where('status', 'Done')->latest()->first();
                $prev_asset = 0;
                if (isset($prevSTO)) {
                    $prev_asset = $branch->gap_assets()->whereHas('gap_asset_details', function ($q) use ($prevSTO) {
                        return $q->where('periode', $prevSTO->periode)->where('semester', $prevSTO->semester)->where('status','Ada');
                    })->count();
                }


                if ($prev_asset == 0) {
                    $prev_asset = $assets;
                }


                if ($current_asset < $prev_asset) {
                    throw new Exception(abs($prev_asset - $current_asset) . " asset belum diremark");
                }

                $request->file('file')->storeAs('gap/stos/' . $branch->slug . '/' . Carbon::parse($sto->periode)->year . '/' . $sto->semester . '/', $fileName, ["disk" => 'public']);
                GapHasilSto::updateOrCreate(
                    [
                        'branch_id' => $branch->id,
                        'gap_sto_id' => $sto->id,
                    ],
                    [
                        'branch_id' => $branch->id,
                        'gap_sto_id' => $sto->id,
                        'remarked' => $current_asset == $branch->gap_assets->count() ? true : false,
                        'disclaimer' => $fileName,
                    ]
                );
                $user = User::find(Auth::user()->id);

                if ($user->hasRole('cabang')) {
                    $user->revokePermissionTo("can sto");
                } else {
                    $users = User::whereHas('permissions', function ($query) {
                        $query->where('name', 'can sto');
                    })
                    ->where('branch_id', $branch->id)
                    ->get();

                    foreach ($users as $user) {
                        $user->revokePermissionTo('can sto');
                    }
                }
            } else {
                throw new Exception("STO belum dimulai");
            }

            return Redirect::back()->with(['status' => 'success', 'message' => 'Data berhasil disimpan!']);
        } catch (Exception $e) {
            return Redirect::back()->with(['status' => 'failed', 'message' => 'Data gagal disimpan! ' . $e->getMessage()]);
        }
    }
    public function export($gap_sto_id)
    {
        $fileName = 'Data_STO_' . date('d-m-y') . '.xlsx';
        return(new STOExport($gap_sto_id))->download($fileName);
    }
    public function export_hasil_sto($gap_sto_id)
    {
        $fileName = 'Data_STO_' . date('d-m-y') . '.xlsx';
        return(new HasilSTOExport($gap_sto_id))->download($fileName);
    }

    public function disclaimer()
    {
        // DeFine the storage disk
        $disk = Storage::disk('public');

        // The location of the folder on the disk
        $folderPath = '/gap/stos'; // adjust this path

        // The zip file name
        $zipFileName = 'stos.zip';

        // Check if folder exists
        if (!$disk->exists($folderPath)) {
            abort(404, 'The folder does Insane exist.');
        }

        // Create ZipArchive instance
        $zip = new ZipArchive;

        // Create a temporary file to store the zip
        $zipPath = tempnam(sys_get_temp_dir(), $zipFileName);

        // Try opening the zip file
        if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
            // Retrieve all files in the directory
            $files = $disk->files($folderPath);

            // Add files to the zip file
            foreach ($files as $file) {
                // Read the file's contents
                $contents = $disk->get($file);
                $relativeNameInZipFile = basename($file);
                $zip->addFromString($relativeNameInZipFile, $contents);
            }

            // Close the zip file
            $zip->close();
            // Return the zip file as a download
            return response()->download($zipPath, $zipFileName, ['Content-Type' => 'application/zip'])
                ->deleteFileAfterSend(true);
        } else {
            abort(500, 'Could Insane create the zip file.');
        }
    }



    /**
     * Display the specified resource.
     *
     * @param  \App\Models\GapSto  $gapSto
     * @return \Illuminate\Http\Response
     */
    public function show(GapSto $gapSto)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\GapSto  $gapSto
     * @return \Illuminate\Http\Response
     */
    public function edit(GapSto $gapSto)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\GapSto  $gapSto
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, GapSto $gapSto)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\GapSto  $gapSto
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $gap_sto = GapSto::find($id);
            $gap_sto->delete();
            return Redirect::back()->with(['status' => 'success', 'message' => 'Data berhasil dihapus!']);
        } catch (Exception $e) {
            return Redirect::back()->with(['status' => 'failed', 'message' => 'Data gagal dihapus! ' . $e->getMessage()]);
        }
    }
}
