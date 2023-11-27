<?php

namespace App\Http\Controllers;

use App\Exports\Scoring\ProjectsExport;
use App\Http\Resources\ScoringProjectsResource;
use App\Imports\GapScoringProjectsImport;
use App\Imports\GapScoringsImport;
use App\Models\Branch;
use App\Models\GapScoring;
use App\Models\GapScoringProject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Throwable;

class GapScoringProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $branchesProps = Branch::get();
        return Inertia::render('GA/Procurement/Scoring/Project/Page', ['branches' => $branchesProps]);
    }


    public function import(Request $request)
    {
        try {
            (new GapScoringProjectsImport)->import($request->file('file'));

            return redirect(route('gap.scoring_projects'))->with(['status' => 'success', 'message' => 'Import Berhasil']);
        } catch (Throwable $e) {
            dd($e);
            return redirect(route('gap.scoring_projects'))->with(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }

    public function export()
    {
        $fileName = 'Data_GAP_Scoring_Projects' . date('d-m-y') . '.xlsx';
        return (new ProjectsExport)->download($fileName);
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
        // dd($request->all());

        try {
            DB::beginTransaction();
            GapScoring::create(
                [
                    'branch_id' => $request->branch_id,
                    'entity' => "BSS",
                    'description' => $request->description,
                    'pic' => $request->pic,
                    'status_pekerjaan' => $request->status_pekerjaan,
                    'dokumen_perintah_kerja' => $request->dokumen_perintah_kerja,
                    'vendor' => $request->vendor,
                    'nilai_project' => $request->nilai_project,
                    'tgl_selesai_pekerjaan' => $request->tgl_selesai_pekerjaan,
                    'tgl_bast' => $request->tgl_bast,
                    'tgl_request_scoring' => $request->tgl_request_scoring,
                    'tgl_scoring' => $request->tgl_scoring,
                    'sla' => $request->sla,
                    'actual' => $request->actual,
                    'meet_the_sla' => $request->actual < $request->sla + 1 ? true : ($request->actual > $request->sla ? false : true),
                    'scoring_vendor' => $request->scoring_vendor,
                    'schedule_scoring' => $request->schedule_scoring,
                    'type' => 'Project',
                    'keterangan' => $request->keterangan,
                ]
            );
            DB::commit();
            return redirect(route('gap.scoring_projects'))->with(['status' => 'success', 'message' => 'Data berhasil disimpan']);
        } catch (Throwable $e) {

            DB::rollBack();
            return redirect(route('gap.scoring_projects'))->with(['status' => 'failed', 'message' => $e->getMessage()]);
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
        // dd($request->all());

        try {
            DB::beginTransaction();
            $branch = Branch::find($request->branch_id);
            $gap_scoring = GapScoring::find($id);
            $gap_scoring->update(
                [
                    'branch_id' => $branch->id,
                    'entity' => "BSS",
                    'description' => $request->description,
                    'pic' => $request->pic,
                    'status_pekerjaan' => $request->status_pekerjaan,
                    'dokumen_perintah_kerja' => $request->dokumen_perintah_kerja,
                    'vendor' => $request->vendor,
                    'nilai_project' => $request->nilai_project,
                    'tgl_selesai_pekerjaan' => $request->tgl_selesai_pekerjaan,
                    'tgl_bast' => $request->tgl_bast,
                    'tgl_request_scoring' => $request->tgl_request_scoring,
                    'tgl_scoring' => $request->tgl_scoring,
                    'sla' => $request->sla,
                    'actual' => $request->actual,
                    'meet_the_sla' => $request->actual < $request->sla + 1 ? true : ($request->actual > $request->sla ? false : true),
                    'scoring_vendor' => $request->scoring_vendor,
                    'schedule_scoring' => $request->schedule_scoring,
                    'type' => 'Project',
                    'keterangan' => $request->keterangan,
                ]
            );
            DB::commit();
            return redirect(route('gap.scoring_projects'))->with(['status' => 'success', 'message' => 'Data berhasil diupdate']);
        } catch (Throwable $e) {

            DB::rollBack();
            return redirect(route('gap.scoring_projects'))->with(['status' => 'failed', 'message' => $e->getMessage()]);
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
            DB::beginTransaction();
            $gap_scoring = GapScoring::find($id);
            $gap_scoring->delete();
            DB::commit();
         return redirect(route('gap.scoring_projects'))->with(['status' => 'success', 'message' => 'Data berhasil dihapus']);
        } catch (Throwable $e) {
            DB::rollBack();
            return redirect(route('gap.scoring_projects'))->with(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }
}
