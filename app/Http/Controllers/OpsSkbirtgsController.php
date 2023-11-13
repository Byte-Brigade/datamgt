<?php

namespace App\Http\Controllers;

use App\Exports\SkBirtgsExport;
use App\Helpers\PaginationHelper;
use App\Http\Resources\SkbirtgsResource;
use App\Imports\SkBirtgsImport;
use App\Models\Branch;
use App\Models\ErrorLog;
use App\Models\OpsSkbirtgs;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Validators\ValidationException;

class OpsSkbirtgsController extends Controller
{
    public function __construct(public OpsSkbirtgs $ops_skbirtgs)
    {
    }

    public function api(Request $request)
    {
        $sortField = 'ops_skbirtgs.id';
        $sortOrder = $request->input('sort_order', 'asc');
        $searchInput = $request->search;
        $filters = $request->filters;


        $query = $this->ops_skbirtgs->select('ops_skbirtgs.*')->orderBy($sortField, $sortOrder)
            ->join('branches', 'ops_skbirtgs.branch_id', 'branches.id');


        $perpage = $request->perpage ?? 10;


        if (isset($filters)) {
            // $filters = array_map(function ($filter) {
            //     return $filter == 'penerima_kuasa' ? "CONCAT('[',employee_positions.position_name,']',' ',employees.name) as penerima_kuasa" : $filter;
            // }, $filters);


            $query->selectRaw(implode(', ', $filters));
        }
        if (!is_null($searchInput)) {
            $searchQuery = "%$searchInput%";
            $query = $query->where('no_surat', 'like', $searchQuery)
                ->orWhere('branch_name', 'like', $searchQuery);
        }

        $query = $query->get();

        $collections = collect([]);

        // Nilai default untuk item ketika tidak ada penerima kuasa


        foreach ($query as $item) {

            // Nilai default untuk item ketika tidak ada penerima kuasa
            $defaultValues = [
                'id' => $item->id,
                'no_surat' => str_contains($item->no_surat, 'SK') ? $item->no_surat : '-',
                'branch_id' => $item->branch_id,
                'status' => $item->status,
                'file' => $item->file,
                'penerima_kuasa' => str_contains($item->status, 'Kanwil') ? $item->status : 'Centralized - SKN',
                'branches' => $item->branches
            ];
            $penerima_kuasa = $item->penerima_kuasa()->get();

            // Jika ada penerima kuasa
            if ($penerima_kuasa->count() > 0) {
                // Buat array sementara untuk menampung item yang telah diubah posisinya
                $tempCollections = [];

                // Jika BM ada, letakkan di posisi pertama
                $bmAdded = false;

                foreach ($penerima_kuasa as $penerima) {
                    $tempItem = array_merge($defaultValues, [
                        'id' => $item->id,
                        'no_surat' => str_contains($item->no_surat, 'SK') ? $item->no_surat : '-',
                        'branch_id' => $item->branch_id,
                        'status' => $item->status,
                        'file' => $item->file,
                        'penerima_kuasa' => '[' . $penerima->getPosition() . ']' . ' ' . $penerima->name,
                        'branches' => $item->branches
                    ]);

                    // Jika 'BM' belum ditambahkan dan saat ini adalah 'BM',
                    // tambahkan 'BM' ke koleksi di posisi pertama
                    if (!$bmAdded && $penerima->getPosition() === 'BM') {
                        array_unshift($tempCollections, $tempItem);
                        $bmAdded = true;
                    } else {
                        // Tambahkan item ke $tempCollections untuk swap nanti
                        $tempCollections[] = $tempItem;
                    }
                }

                // Menukar posisi item pada $tempCollections (mulai dari item ke-9)
                $count = count($tempCollections);
                for ($i = 8; $i < $count; $i += 2) {
                    if ($i + 1 < $count) {
                        $temp = $tempCollections[$i];
                        $tempCollections[$i] = $tempCollections[$i + 1];
                        $tempCollections[$i + 1] = $temp;
                    }
                }

                // Menambahkan item yang telah ditukar ke $collections
                $collections = $collections->merge(collect($tempCollections));
            } else {
                // Jika tidak ada penerima kuasa, tambahkan item dengan nilai default (null)
                $collections->push($defaultValues);
            }
        }
        return response()->json(PaginationHelper::paginate($collections, 15));
    }



    public function index()
    {
        $branchesProps = Branch::get();
        return Inertia::render('Ops/SKBIRTGS/Page', ['branches' => $branchesProps]);
    }

    public function import(Request $request)
    {
        try {
            (new SkBirtgsImport)->import($request->file('file'));

            return redirect(route('ops.skbirtgs'))->with(['status' => 'success', 'message' => 'Import Berhasil']);
        } catch (ValidationException $e) {
            $failures = $e->failures();
            $list_error = collect([]);
            foreach ($failures as $failure) {
                $failure->row(); // row that went wrong
                $failure->attribute(); // either heading key (if using heading row concern) or column index
                $failure->errors(); // Actual error messages from Laravel validator
                $failure->values(); // The values of the row that has failed.
                $error = ErrorLog::create([
                    'row' => $failure->row(),
                    'attribute' => $failure->row(),
                    'error_message' => $failure->errors(),
                    'value' => $failure->values(),
                ]);

                $list_error->push($error);
            }
            return redirect(route('ops.skbirtgs'))->with(['status' => 'failed', 'message' => 'Import Failed']);
        }
    }

    public function upload(Request $request, $id)
    {
        try {
            $ops_skbirtgs = OpsSkbirtgs::find($id);

            $fileName = $request->file('file')->getClientOriginalName();
            $request->file('file')->storeAs('ops/skbirtgs/', $fileName, ["disk" => 'public']);

            $ops_skbirtgs->file = $fileName;
            $ops_skbirtgs->save();

            return redirect(route('ops.skbirtgs'))->with(['status' => 'success', 'message' => 'File berhasil diupload!']);
        } catch (Exception $e) {
            dd($e);

            return redirect(route('ops.skbirtgs'))->with(['status' => 'failed', 'message' => 'File gagal diupload!']);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $ops_skbirtgs = OpsSkbirtgs::find($id);
            $ops_skbirtgs->update([
                'no_surat' => $request->no_surat,
                'status' => $request->status,
            ]);
            return redirect(route('ops.skbirtgs'))->with(['status' => 'success', 'message' => 'Data berhasil diubah']);
        } catch (Exception $e) {
            return redirect(route('ops.skbirtgs'))->with(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        try {
            $ops_skbirtgs = OpsSkbirtgs::find($id);
            $ops_skbirtgs->delete();
            return redirect(route('ops.skbirtgs'))->with(['status' => 'success', 'message' => 'Data berhasil dihapus']);
        } catch (Exception $e) {
            return redirect(route('ops.skbirtgs'))->with(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }

    public function export(Request $request)
    {
        $fileName = 'Data_SK_BI_RTGS_' . date('d-m-y') . '.xlsx';

        return (new SkBirtgsExport($request->branch))->download($fileName);
    }
}
