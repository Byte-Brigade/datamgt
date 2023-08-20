<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use App\Imports\UsersImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class UsersController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Home/Page', [
            'users' => User::search($request->search)
                ->orderBy('id', 'asc')
                ->paginate($request->perpage ?? 10)
                ->appends('query', null)
                ->withQueryString()
        ]);
    }

    public function import(Request $request)
    {
        Excel::import(new UsersImport, $request->file('file')->store('temp'));

        return redirect('home')->with(['status' => 'success', 'message' => 'Import Success']);
    }
}
