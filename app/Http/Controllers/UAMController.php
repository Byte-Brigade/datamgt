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

    public function __construct(public User $user)
    {
    }

    public function api(Request $request)
    {
        $sortField = 'id';
        $sortOrder = $request->input('sort_order', 'asc');
        $searchInput = $request->search;
        $query = $this->user->orderBy($sortField, $sortOrder);
        $perpage = $request->perpage ?? 10;

        //  if (!is_null($searchInput)) {
        //      $searchQuery = "%$searchInput%";
        //      $query = $query->where('tgl_speciment', 'like', $searchQuery);
        //  }
        $users = $query->paginate($perpage);
        return UserResource::collection($users);
    }

    public function index()
    {
        $branchesProps = Branch::get();
        $positionProps = Role::where('name', '!=', 'superadmin')->get();
        $permissionProps = Permission::get();
        return Inertia::render('UAM/Page', ['branches' => $branchesProps, 'positions' => $positionProps, 'permissions' => $permissionProps]);
    }

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
            $name = strtolower($request->name);
            $names = explode(' ', $name);
            if (count($names) > 1) {
                $email = $names[0] . '.' . end($names);
            } else {
                $email = $names[0] . '.' . $names[0];
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $email . '@banksampoerna.com',
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

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }


}
