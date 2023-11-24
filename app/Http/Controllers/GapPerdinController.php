<?php

namespace App\Http\Controllers;

use App\Helpers\PaginationHelper;
use App\Http\Resources\PerdinResource;
use App\Imports\PerdinImport;
use App\Models\Branch;
use App\Models\GapPerdin;
use App\Models\GapPerdinDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Throwable;

class GapPerdinController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $query = GapPerdin::get();

        // dd($query->groupBy('divisi_pembebanan')->map(function ($perdins, $divisi) {
        //     return ['divisi_pembebanan' => $divisi,
        //     'airline' => $perdins->where('category','Airline')->map(function($item) {
        //         return $item->value;
        //     }),
        //     'ka' => $perdins->where('category','KA')->sum('value'),
        //     'hotel' => $perdins->where('category','Hotel')->sum('value'),
        //     'total' => $perdins->sum('value')];
        // })->sortByDesc(function($item) {
        //     return $item['total'];
        // }));

        return Inertia::render('GA/Procurement/Perdin/Page');
    }

    protected array $sortFields = ['divisi_pembebanan', 'periode', 'value'];

    public function api(GapPerdin $gap_perdin, Request $request)
    {
        $sortFieldInput = $request->input('sort_field', 'divisi_pembebanan');
        $sortField = in_array($sortFieldInput, $this->sortFields) ? $sortFieldInput : 'divisi_pembebanan';
        $sortOrder = $request->input('sort_order', 'asc');
        $searchInput = $request->search;
        $query = $gap_perdin->select('gap_perdins.*')->orderBy($sortField, $sortOrder);

        $perpage = $request->perpage ?? 10;

        if (!is_null($request->branch_code)) {
            $query = $query->where('branch_code', $request->branch_code);
        }

        if (!is_null($searchInput)) {
            $searchQuery = "%$searchInput%";
            $query = $query->where(function ($query) use ($searchQuery) {
                $query->where('divisi_pembebanan', 'like', $searchQuery)
                    ->orWhere('category', 'like', $searchQuery);
            });
        }

        $query = $query->get();

        $collections = $query->groupBy('divisi_pembebanan')->map(function ($perdins, $divisi) {
            return [
                'divisi_pembebanan' => $divisi,
                'airline' => $perdins->where('category', 'Airline')->sum('value'),
                'ka' => $perdins->where('category', 'KA')->sum('value'),
                'hotel' => $perdins->where('category', 'Hotel')->sum('value'),
                'total' => $perdins->sum('value')
            ];
        })->sortByDesc(function ($item) {
            return $item['total'];
        });
        return response()->json(PaginationHelper::paginate($collections, $perpage));;
    }

    public function api_detail(GapPerdin $gap_perdin, Request $request, $divisi_pembebanan)
    {
        $sortFieldInput = $request->input('sort_field', 'periode');
        $sortField = in_array($sortFieldInput, $this->sortFields) ? $sortFieldInput : 'divisi_pembebanan';
        $sortOrder = $request->input('sort_order', 'asc');
        $searchInput = $request->search;
        $query = $gap_perdin->select('gap_perdins.*')->where('divisi_pembebanan', $divisi_pembebanan)->orderBy($sortField, $sortOrder);

        $perpage = $request->perpage ?? 10;

        if (!is_null($request->branch_code)) {
            $query = $query->where('branch_code', $request->branch_code);
        }

        if (!is_null($searchInput)) {
            $searchQuery = "%$searchInput%";
            $query = $query->where(function ($query) use ($searchQuery) {
                $query->where('divisi_pembebanan', 'like', $searchQuery)
                    ->orWhere('category', 'like', $searchQuery);
            });
        }

        $data = $query->paginate($perpage);
        return PerdinResource::collection($data);
    }

    public function import(Request $request)
    {
        try {
            DB::beginTransaction();
            (new PerdinImport)->import($request->file('file'));
            DB::commit();
            return redirect(route('gap.perdins'))->with(['status' => 'success', 'message' => 'Import Berhasil']);
        } catch (Throwable $e) {
            DB::rollBack();
            dd($e);
            return redirect(route('gap.perdins'))->with(['status' => 'failed', 'message' => $e->getMessage()]);
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
        //
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function detail($divisi_pembebanan)
    {
        return Inertia::render('GA/Procurement/Perdin/Detail', ['divisi_pembebanan' => $divisi_pembebanan]);
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
        //
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
