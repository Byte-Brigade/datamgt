<?php

namespace App\Http\Controllers;

use App\Exports\SkOperasionalExport;
use App\Helpers\PaginationHelper;
use Exception;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\OpsSkOperasional;
use App\Imports\SkOperasionalsImport;
use App\Http\Resources\SkOperasionalResource;
use App\Models\Branch;
use Maatwebsite\Excel\Validators\ValidationException;

class OpsSkOperasionalController extends Controller
{
    protected array $sortFields = ['ops_sk_operasionals.id', 'branches.branch_code', 'branches.branch_name'];

    public function __construct(public OpsSkOperasional $ops_sk_operasional)
    {
    }

    public function api(Request $request)
    {
        $sortFieldInput = $request->input('sort_field', 'ops_sk_operasionals.id');
        $sortField = in_array($sortFieldInput, $this->sortFields) ? $sortFieldInput : 'ops_sk_operasionals.id';
        $sortOrder = $request->input('sort_order', 'asc');
        $searchInput = $request->search;
        $query = $this->ops_sk_operasional->select('ops_sk_operasionals.*')->orderBy($sortField, $sortOrder)
            ->join('branches', 'ops_sk_operasionals.branch_id', 'branches.id');
        $perpage = $request->perpage ?? 10;

        if (!is_null($searchInput)) {
            $searchQuery = "%$searchInput%";
            $query = $query->where('no_surat', 'like', $searchQuery)
                ->orWhere('branch_code', 'like', $searchQuery)
                ->orWhere('branch_name', 'like', $searchQuery);
        }
        // $sk_operasional = $query->paginate($perpage);
        $query = $query->get();

        $collections = collect([]);

        // Nilai default untuk item ketika tidak ada penerima kuasa


        foreach ($query as $item) {

            // Nilai default untuk item ketika tidak ada penerima kuasa
            $defaultValues = [
            'id' => $item->id,
            'no_surat' => $item->no_surat,
            'branch_id' => $item->branch_id,
            'expiry_date' => $item->expiry_date,
            'note' => $item->note,
            'file' => $item->file,
            'penerima_kuasa' => '-',
            'branch_name' => $item->branches->branch_name,
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
                        'branch_name' => $item->branches->branch_name,
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

        return Inertia::render('Ops/SkOperasional/Page', ['branches' => $branchesProps]);
    }

    public function import(Request $request)
    {
        try {
            (new SkOperasionalsImport)->import($request->file('file'));

            return redirect(route('ops.sk-operasional'))->with(['status' => 'success', 'message' => 'Import Berhasil']);
        } catch (ValidationException $e) {
            $failures = $e->failures();
            dd($failures);
            $list_error = collect([]);
            // foreach ($failures as $failure) {
            //     $failure->row(); // row that went wrong
            //     $failure->attribute(); // either heading key (if using heading row concern) or column index
            //     $failure->errors(); // Actual error messages from Laravel validator
            //     $failure->values(); // The values of the row that has failed.
            //     $error = ErrorLog::create([
            //         'row' => $failure->row(),
            //         'attribute' => $failure->row(),
            //         'error_message' => $failure->errors(),
            //         'value' => $failure->values(),
            //     ]);

            //     $list_error->push($error);
            // }
            return redirect(route('ops.sk-operasional'))->with(['status' => 'failed', 'message' => 'Import Failed']);
        }
    }

    public function export(Request $request)
    {
        $fileName = 'Data_SK_Operasional_' . date('d-m-y') . '.xlsx';
        return (new SkOperasionalExport($request->branch))->download($fileName);
    }

    public function upload(Request $request, $id)
    {
        try {
            $ops_skoperasional = OpsSkOperasional::find($id);

            $fileName = $request->file('file')->getClientOriginalName();
            $request->file('file')->storeAs('ops/skoperasional/', $fileName, ["disk" => 'public']);

            $ops_skoperasional->file = $fileName;
            $ops_skoperasional->save();

            return redirect(route('ops.sk-operasional'))->with(['status' => 'success', 'message' => 'File berhasil diupload!']);
        } catch (Exception $e) {
            dd($e);

            return redirect(route('ops.sk-operasional'))->with(['status' => 'failed', 'message' => 'File gagal diupload!']);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $ops_skoperasional = OpsSkOperasional::find($id);
            $ops_skoperasional->update([
                'no_surat' => $request->no_surat,
                'expiry_date' => $request->expiry_date,

            ]);
            return redirect(route('ops.sk-operasional'))->with(['status' => 'success', 'message' => 'Data berhasil diubah']);
        } catch (Exception $e) {
            return redirect(route('ops.sk-operasional'))->with(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        try {
            $ops_skoperasional = OpsSkOperasional::find($id);
            $ops_skoperasional->delete();
            return redirect(route('ops.sk-operasional'))->with(['status' => 'success', 'message' => 'Data berhasil dihapus']);
        } catch (Exception $e) {
            return redirect(route('ops.sk-operasional'))->with(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }
}
