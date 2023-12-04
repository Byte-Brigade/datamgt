<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;

class DashboardApiController extends Controller
{
    public function branches(Branch $branch, Request $request)
    {
        $params = $request->all();
        // $query = $branch->select('branches.*')->join('branch_types', 'branches.branch_type_id', 'branch_types.id');

        $branches = Branch::with('branch_types');

        if (isset($params['area']) && $params['area'] != 'none') {
            $area = $params['area'];
            $branches->where('area', $area);
        }

        if (isset($params['branch_code']) && $params['branch_code'] != 0) {
            $branch_code = $params['branch_code'];
            $branches->where('id', $branch_code);
        }

        $branches = $branches->get();
        $jumlah_cabang = $branches->sortBy('branch_code')->groupBy('branch_types.alt_name');
        $data = [
            'branches' => $branches,
            'jumlah_cabang' => $jumlah_cabang
        ];

        return response()->json($data);
    }
}
