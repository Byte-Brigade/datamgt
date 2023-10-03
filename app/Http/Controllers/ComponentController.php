<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\BranchType;
use Illuminate\Http\Request;

class ComponentController extends Controller
{
    public function branches(Request $request)
    {
        $field = $request->get('field');
        $column = $this->handleColumn($field);

        $data = Branch::get()->pluck($column)->unique()->toArray();

        return response()->json(['data' => $data, 'field' => $field, 'status' => "success"]);
    }

    public function branch_types(Request $request)
    {

        $field = $request->get('field');
        $column = $this->handleColumn($field);
        $data = BranchType::get()->pluck($column)->unique()->toArray();

        return response()->json(['data' => $data, 'field' => $field, 'status' => "success"]);
    }


    private function handleColumn($column)
    {
        if (str_contains($column, '.')) {
            $arr = explode('.', $column);
            $column = array_shift($arr);
            $column = $arr[0];
        }
        return $column;
    }
}
