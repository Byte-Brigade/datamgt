<?php

namespace App\Http\Controllers;

use App\Exports\InfraScoring\InfraScoringExport;
use App\Http\Resources\ScoringAssessmentsResource;
use App\Imports\InfraScoringAssessmentsImport;
use App\Models\Branch;
use App\Models\InfraScoring;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Throwable;

class InfraScoringAssessmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $branches = Branch::get();
        return Inertia::render('GA/Infra/Scoring/Assessment/Page', ['branches' => $branches]);
    }

    public function export()
    {
        $fileName = 'Data_Infra_Scoring_Assessment_' . date('d-m-y') . '.xlsx';
        return (new InfraScoringExport('Assessment'))->download($fileName);
    }


    public function import(Request $request)
    {
        try {
            (new InfraScoringAssessmentsImport)->import($request->file('file'));

            activity()->enableLogging();
            activity("InfraScoringAssessment")
                ->event("imported")
                ->log("This model has been imported");

            return redirect(route('infra.scoring-assessments'))->with(['status' => 'success', 'message' => 'Import Berhasil']);
        } catch (Throwable $e) {
            dd($e);
            return redirect(route('infra.scoring-assessments'))->with(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }



    public function template()
    {
        $path = 'app\public\templates\template_infra_assessments.xlsx';

        return response()->download(storage_path($path));
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
        return Inertia::render('GA/Infra/Scoring/Assessment/Detail', ['scoring_vendor' => $scoring_vendor, 'branches' => $branches]);
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
            $infra_scoring = InfraScoring::find($id);
            $infra_scoring->update(
                [
                    'branch_id' => $branch->id,
                    'entity' => "BSS",
                    'description' => $request->description,
                    'pic' => $request->pic,
                    'dokumen_perintah_kerja' => $request->dokumen_perintah_kerja,
                    'vendor' => $request->vendor,
                    'tgl_scoring' => $request->tgl_scoring,
                    'scoring_vendor' => $request->scoring_vendor,
                    'schedule_scoring' => $request->schedule_scoring,
                    'type' => 'Assessment',
                    'keterangan' => $request->keterangan,
                ]
            );
            DB::commit();
            return Redirect::back()->with(['status' => 'success', 'message' => 'Data berhasil diupdate']);
        } catch (Throwable $e) {

            DB::rollBack();
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
        try {
            DB::beginTransaction();
            $infra_scoring = InfraScoring::find($id);
            $infra_scoring->delete();
            DB::commit();
         return redirect(route('infra.scoring-assessments'))->with(['status' => 'success', 'message' => 'Data berhasil dihapus']);
        } catch (Throwable $e) {
            DB::rollBack();
            return redirect(route('infra.scoring-assessments'))->with(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }
}
