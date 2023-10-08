<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Throwable;

class UAMController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

     public function api(User $user, Request $request)
     {
         $sortField = 'id';
         $sortOrder = $request->input('sort_order', 'asc');
         $searchInput = $request->search;
         $query = $user->orderBy($sortField, $sortOrder);
         $perpage = $request->perpage ?? 10;

        //  if (!is_null($searchInput)) {
        //      $searchQuery = "%$searchInput%";
        //      $query = $query->where('tgl_speciment', 'like', $searchQuery);
        //  }
         $employees = $query->paginate($perpage);
         return UserResource::collection($employees);
     }

    public function index()
    {
        $branchesProps = Branch::get();
        $positionProps = Role::where('name', '!=', 'superadmin')->get();
        $permissionProps = Permission::get();
        return Inertia::render('UAM/Page', ['branches' => $branchesProps, 'positions' => $positionProps, 'permissions' => $permissionProps]);
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
            $user = User::create([
                'name' => $request->name,
                'email' => rand(1, 1000) .'aawd'.rand(1, 1000).'@gmail.com',
                'nik' => $request->nik,
                'password' => Hash::make($request->password),
            ]);
            $user->assignRole($request->position);
            $user->syncPermissions($request->permissions);
            return redirect(route('uam'))->with(['status' => 'success', 'message' => 'User berhasil dibuat']);
        } catch (Throwable $th) {
            dd($th);
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
