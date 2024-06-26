<?php

namespace App\Http\Controllers;

use App\Exports\GapScoring\GapScoringExport;
use App\Exports\Scoring\AssessmentsExport;
use App\Http\Resources\ScoringAssessmentsResource;
use App\Imports\GapScoringAssessmentsImport;
use App\Models\Branch;
use App\Models\GapScoring;
use App\Models\GapScoringAssessment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Maatwebsite\Excel\Validators\ValidationException;
use Throwable;

class GapScoringAssessmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $branches = Branch::get();
        return Inertia::render('GA/Procurement/Scoring/Assessment/Page', ['branches' => $branches]);
    }

    public function import(Request $request)
    {
        try {
            (new GapScoringAssessmentsImport)->import($request->file('file'));

            activity()->enableLogging();
            activity("GapScoringAssessment")
                ->event("imported")
                ->log("This model has been imported");

            return Redirect::back()->with(['status' => 'success', 'message' => 'Import Berhasil']);
        } catch (ValidationException $e) {
            $errorString = '';
            /** @var array $messages */
            foreach ($e->errors() as $field => $messages) {
                foreach ($messages as $message) {
                    $errorString .= "{$field}: {$message},";
                }
            }
            $errorString = trim($errorString);

            return Redirect::back()->with(['status' => 'failed', 'message' => $errorString]);
        } catch (\Throwable $th) {
            return Redirect::back()->with(['status' => 'failed', 'message' => $th->getMessage()]);
        }
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
            DB::beginTransaction();
            GapScoring::create(
                [
                    'branch_id' => $request->branch_id,
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
            return redirect(route('gap.scoring-assessments'))->with(['status' => 'success', 'message' => 'Data berhasil disimpan']);
        } catch (Throwable $e) {
            DB::rollBack();
            return redirect(route('gap.scoring-assessments'))->with(['status' => 'failed', 'message' => $e->getMessage()]);
        }
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
        return Inertia::render('GA/Procurement/Scoring/Assessment/Detail', ['scoring_vendor' => $scoring_vendor, 'branches' => $branches]);
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
            return redirect(route('gap.scoring-assessments'))->with(['status' => 'success', 'message' => 'Data berhasil diupdate']);
        } catch (Throwable $e) {

            DB::rollBack();
            return redirect(route('gap.scoring-assessments'))->with(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }

    public function export()
    {
        $fileName = 'Data_GAP_Scoring_Assessment_' . date('d-m-y') . '.xlsx';
        return (new GapScoringExport('Assessment'))->download($fileName);
    }

    public function template()
    {
        $path = 'app\public\templates\template_gap_assessments.xlsx';

        return response()->download(storage_path($path));
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
            return redirect(route('gap.scoring-assessments'))->with(['status' => 'success', 'message' => 'Data berhasil dihapus']);
        } catch (Throwable $e) {
            DB::rollBack();
            return redirect(route('gap.scoring-assessments'))->with(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }
}
