<?php

namespace App\Http\Controllers;

use App\Exports\Assets\AssetsExport;
use App\Imports\AssetsImport;
use App\Models\Branch;
use App\Models\GapAsset;
use Illuminate\Http\Request;
use App\Http\Resources\AssetsResource;
use Inertia\Inertia;
use Throwable;

class GapAssetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $branchesProps = Branch::get();
        return Inertia::render('GA/Asset/Page', ['branches' => $branchesProps]);
    }

    protected array $sortFields = ['branches.branch_code',  'date_in_place_service', 'asset_number', 'category', 'date_in_place_service', 'net_book_value', 'depre_exp'];

    public function api(GapAsset $gap_asset, Request $request)
    {
        $sortFieldInput = $request->input('sort_field', 'branches.branch_code');
        $sortField = in_array($sortFieldInput, $this->sortFields) ? $sortFieldInput : 'branches.branch_code';
        $sortOrder = $request->input('sort_order', 'asc');
        $searchInput = $request->search;
        $query = $gap_asset->select('gap_assets.*')->orderBy($sortField, $sortOrder)
            ->join('branches', 'gap_assets.branch_id', 'branches.id');

        $perpage = $request->perpage ?? 10;

        if (!is_null($searchInput)) {
            $searchQuery = "%$searchInput%";
            $query = $query->where(function ($query) use ($searchQuery) {
                $query->where('asset_number', 'like', $searchQuery)
                    ->orWhere('category', 'like', $searchQuery)
                    ->orWhereHas('branches', function ($q) use ($searchQuery) {
                        $q->where('branch_name', 'like', $searchQuery);
                    });
            });
        }
        $data = $query->paginate($perpage);
        return AssetsResource::collection($data);
    }

    public function import(Request $request)
    {
        try {
            (new AssetsImport)->import($request->file('file'));

            return redirect(route('gap.assets'))->with(['status' => 'success', 'message' => 'Import Berhasil']);
        } catch (Throwable $e) {
            dd($e);
            return redirect(route('gap.assets'))->with(['status' => 'failed', 'message' => $e->getMessage()]);
        }
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
