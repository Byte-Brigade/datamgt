<?php

namespace App\Http\Controllers;

use App\Exports\InfraScoring\InfraScoringExport;
use App\Http\Resources\ScoringProjectsResource;
use App\Imports\InfraScoringProjectsImport;
use App\Models\Branch;
use App\Models\InfraScoring;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Throwable;

class InfraScoringProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $branches = Branch::get();
        return Inertia::render('GA/Infra/Scoring/Project/Page', ['branches' => $branches]);
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

    public function template()
    {
        $path = 'app\public\templates\template_infra_projects.xlsx';

        return response()->download(storage_path($path));
    }


    public function import(Request $request)
    {
        try {
            (new InfraScoringProjectsImport)->import($request->file('file'));

            activity()->enableLogging();
            activity("InfraScoringProject")
                ->event("imported")
                ->log("This model has been imported");

            return redirect(route('infra.scoring-projects'))->with(['status' => 'success', 'message' => 'Import Berhasil']);
        } catch (Throwable $e) {
            dd($e);
            return redirect(route('infra.scoring-projects'))->with(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }



    public function export()
    {
        $fileName = 'Data_Infra_Scoring_Project_' . date('d-m-y') . '.xlsx';
        return (new InfraScoringExport('Post Project'))->download($fileName);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function detail($scoring_vendor)
    {
        $branches = Branch::get();
        return Inertia::render('GA/Infra/Scoring/Project/Detail', ['scoring_vendor' => $scoring_vendor, 'branches' => $branches, 'status_pekerjaan' => InfraScoring::whereNot('type', 'Assessment')->pluck('status_pekerjaan')->unique()->toArray()]);
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
            $scoring_project = InfraScoring::find($id);
            $branch = Branch::find($request->branch_id);
            $scoring_project->update([
                'branch_id' => $branch->id,
                'entity' => "BSS",
                'description' => $request->description,
                'status_pekerjaan' => $request->status_pekerjaan,
                'pic' => $request->pic,
                'dokumen_perintah_kerja' => $request->dokumen_perintah_kerja,
                'vendor' => $request->vendor,
                'tgl_scoring' => $request->tgl_scoring,
                'tgl_selesai_pekerjaan' => $request->tgl_selesai_pekerjaan,
                'tgl_bast' => $request->tgl_bast,
                'tgl_request_scoring' => $request->tgl_request_scoring,
                'tgl_selesai' => $request->tgl_selesai,
                'sla' => $request->sla,
                'actual' => $request->actual,
                'meet_the_sla' => $request->actual < $request->sla ? true : ($request->actual  > ($request->sla - 1) ? false : true),
                'scoring_vendor' => $request->scoring_vendor,
                'schedule_scoring' => $request->schedule_scoring,
                'type' => 'Post Project',
                'keterangan' => $request->keterangan,
            ]);

            return Redirect::back()->with(['status' => 'success', 'message' => 'Data berhasil diupdate']);
        } catch (Throwable $e) {;
            return Redirect::back()->with(['status' => 'failed', 'message' => $e->getMessage()]);
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
        //
    }
}
