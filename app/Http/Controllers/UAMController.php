<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\Branch;
use App\Models\User;
use Exception;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Throwable;
use Illuminate\Support\Str;

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
        $query = $this->user->whereHas('roles', function ($q) {
            return $q->where('name', '!=', 'superadmin');
        })->orderBy($sortField, $sortOrder);
        $perpage = $request->perpage ?? 15;

        //  if (!is_null($searchInput)) {
        //      $searchQuery = "%$searchInput%";
        //      $query = $query->where('tgl_speciment', 'like', $searchQuery);
        //  }
        $users = $query->paginate($perpage);
        return UserResource::collection($users);
    }

    public function index()
    {
        $branchesProps = Branch::with('branch_types')->get()->prepend(['branch_name' => 'All', 'branch_code' => 'none']);

        $positionProps = Role::where('name', '!=', 'superadmin')->get();
        $permissionProps = Permission::get();
        return Inertia::render('UAM/Page', ['branches' => $branchesProps, 'positions' => $positionProps, 'permissions' => $permissionProps]);
    }

    public function request_reset_passwrd(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }

    // public function reset_password(Request $request)
    // {
    //     $request->validate([
    //         'token' => 'required',
    //         'email' => 'required|email',
    //         'password' => 'required|min:8|confirmed',
    //     ]);

    //     $status = Password::reset(
    //         $request->only('email', 'password', 'password_confirmation', 'token'),
    //         function ($user, $password) {
    //             $user->forceFill([
    //                 'password' => Hash::make($password)
    //             ])->setRememberToken(Str::random(60));

    //             $user->save();

    //             event(new PasswordReset($user));
    //         }
    //     );

    //     return $status === Password::PASSWORD_RESET
    //         ? redirect()->route('login')->with('status', __($status))
    //         : back()->withErrors(['email' => [__($status)]]);
    // }


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
        $request->validate([
            'password' => 'required|min:8|confirmed',
        ]);
        try {

            $full_access = [
                'can view',
                'can edit',
                'can delete',
                'can add',
                'can export',
                'can sto'
            ];

            $name = strtolower($request->name);
            $names = explode(' ', $name);
            if (count($names) > 1) {
                $email = $names[0] . '.' . end($names);
            } else {
                $email = $names[0] . '.' . $names[0];
            }

            $email = $email . '@banksampoerna.com';

            $user = User::create([
                'name' => $request->name,
                'email' => $email,
                'nik' => $request->nik,
                'password' => Hash::make($request->password),
                'branch_id' => $request->branch_id != 0 ? $request->branch_id : null,
            ]);
            $user->assignRole($request->position);
            if ($request->position == 'admin') {
                $user->syncPermissions($full_access);
            } else {
                $user->syncPermissions($request->permissions);
            }

            return redirect(route('uam'))->with(['status' => 'success', 'message' => 'User berhasil dibuat']);
        } catch (Throwable $th) {
            dd($th);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'password' => 'min:8|confirmed',
        ]);

        try {
            $user = User::find($id);
            $role = Role::where('name', $request->position)->pluck('name')->first();
            $user->update([
                'name' => $request->name,
                'nik' => $request->nik,
                'password' => Hash::make($request->password)
            ]);
            $user->syncRoles($role);
            $user->syncPermissions($request->permissions);
            return redirect(route('uam'))->with(['status' => 'success', 'message' => 'Data berhasil diubah']);
        } catch (Exception $e) {
            return redirect(route('uam'))->with(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        try {
            $user = User::find($id);
            $user->delete();
            return redirect(route('uam'))->with(['status' => 'success', 'message' => 'Data berhasil dihapus']);
        } catch (Exception $e) {
            return redirect(route('uam'))->with(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }
}
