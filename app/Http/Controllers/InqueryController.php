<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InqueryController extends Controller
{
    public function branch()
    {
        $branches = Branch::with([
            'branch_types',
            'employees' => function ($query) {
                return $query->whereHas('employee_positions', function ($q) {
                    return $q->where('position_name', 'BM');
                });
            }
        ])->paginate(15);
        return Inertia::render('Inquery/Branch/Page', [
            'branches' => $branches
        ]);
    }

    public function branchDetail($id)
    {
        $branch = Branch::where('branch_code', $id)->firstOrFail();
        return Inertia::render('Inquery/Branch/Detail', [
            'branch' => $branch
        ]);
    }
}
