<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\BranchType;
use Illuminate\Http\Request;

class ComponentController extends Controller
{
    public function branches(){
        $data = Branch::get()->pluck('branch_name','id');

        return response()->json(['data' => $data, 'status' => "success"]);
    }

    public function branch_types(){
        $data = BranchType::get()->pluck('name','id');

        return response()->json(['data' => $data, 'status' => "success"]);
    }
}
