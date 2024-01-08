<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\GapSto;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;

class GapStoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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

            $branch = Branch::with('gap_assets')->where('branch_code', $request->branch_code)->first();

            $fileName = $request->file('file')->getClientOriginalName();
            $request->file('file')->storeAs('gap/stos/' . $branch->slug . '/', $fileName, ["disk" => 'public']);
            GapSto::updateOrCreate(
                ['branch_id' => $branch->id],
                [
                    'branch_id' => $branch->id,
                    'remarked' => $branch->gap_assets->whereNotNull('remark')->count() == $branch->gap_assets->count() ? true : false,
                    'disclaimer' => $fileName,
                    'periode' => Carbon::now()->format('Y-m-d'),
                ]
            );
            User::find(Auth::user()->id)->revokePermissionTo("can sto");
            return redirect(route('inquery.assets'))->with(['status' => 'success', 'message' => 'Data berhasil disimpan!']);
        } catch (Exception $e) {
            dd($e->getMessage());
            return redirect(route('inquery.assets'))->with(['status' => 'failed', 'message' => 'Data gagal disimpan! ' . $e->getMessage()]);
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
