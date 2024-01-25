<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\EmployeePositionResource;
use App\Models\EmployeePosition;
use Illuminate\Http\Request;

class DashboardApiController extends Controller
{
    public function employee_positions(EmployeePosition $employee_position, Request $request)
    {
        $sortFieldInput = $request->input('sort_field') ?? 'position_name';
        $sortOrder = $request->input('sort_order', 'asc');
        $searchInput = $request->search;
        $query = $employee_position->select('employee_positions.*')->orderBy($sortFieldInput, $sortOrder);
        $perpage = $request->perpage ?? 15;


        $data = $query->paginate($perpage);


        return EmployeePositionResource::collection($data);
    }
}
