<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);
        $credentials = request(['email', 'password']);

        $user = User::where('email', $credentials['email'])
            ->orWhere('nik', strtolower($credentials['email']))
            ->first();

        if ($user == null) {
            return response([
                'success' => false,
                'message' => ['login failed.']
            ], 404);
        }

        if (!Auth::attempt($credentials)) {
            return response([
                'success' => false,
                'message' => ['These credentials do not match our records.']
            ], 404);
        }

        $token = $user->createToken('auth_token', ['*'], now()->addMinutes(60));

        $response = [
            'success' => true,
            'user' => $user,
            'token' => $token->plainTextToken,
            'message' => ['Login success.']
        ];

        return response($response, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $user = $request->user();
        $user->currentAccessToken()->delete();
        $response = [
            'success' => true,
            'message' => 'Logout Success.'
        ];

        return response($response, 201);
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
