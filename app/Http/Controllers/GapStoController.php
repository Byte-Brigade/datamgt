<?php

namespace App\Http\Controllers;

use App\Exports\STO\STOExport;
use App\Models\Branch;
use App\Models\GapAssetDetail;
use App\Models\GapHasilSto;
use App\Models\GapSto;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
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
        return Inertia::render('GA/Procurement/STO/Detail', ['gap_sto_id' => $gap_sto_id]);
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
            GapSto::create([
                'periode' => Carbon::parse($request->periode)->startOfMonth()->format('Y-m-d'),
                'semester' => $request->semester,
                'status' => 'On Progress',
                'keterangan' => $request->keterangan,
            ]);
            return Redirect::back()->with(['status' => 'success', 'message' => 'STO berhasil dibuat!']);
        } catch (Exception $e) {
            dd($e->getMessage());
            return Redirect::back()->with(['status' => 'failed', 'message' => 'Data gagal disimpan! ' . $e->getMessage()]);
        }
    }

    public function store_hasil_sto(Request $request, $slug)
    {
        // dd($slug);
        try {

            $branch = Branch::with('gap_assets')->where('slug', $slug)->first();
            $fileName = $request->file('file')->getClientOriginalName();
            $request->file('file')->storeAs('gap/stos/' . $branch->slug . '/', $fileName, ["disk" => 'public']);

            $periodeSto = GapSto::max('periode');
            $sto = GapSto::where('status', 'On Progress')->where('periode', $periodeSto)->first();
            if (isset($sto)) {
                $hasil_sto = GapHasilSto::updateOrCreate(
                    ['branch_id' => $branch->id],
                    [
                        'branch_id' => $branch->id,
                        'gap_sto_id' => $sto->id,
                        'remarked' => $branch->gap_assets->whereNotNull('remark')->count() == $branch->gap_assets->count() ? true : false,
                        'disclaimer' => $fileName,
                    ]
                );

                GapAssetDetail::where('periode', $sto->periode)->where('semester', $sto->semester)->update(['sto' => true]);
                User::find(Auth::user()->id)->revokePermissionTo("can sto");
            } else {
                throw new Exception("STO belum dimulai");
            }

            return Redirect::back()->with(['status' => 'success', 'message' => 'Data berhasil disimpan!']);
        } catch (Exception $e) {
            dd($e->getMessage());
            return Redirect::back()->with(['status' => 'failed', 'message' => 'Data gagal disimpan! ' . $e->getMessage()]);
        }
    }
    public function export()
    {
        $fileName = 'Data_STO_' . date('d-m-y') . '.xlsx';
        return (new STOExport)->download($fileName);
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
    public function destroy(GapSto $gapSto)
    {
        //
    }
}
