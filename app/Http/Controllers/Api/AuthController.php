<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use COM;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;
use Laravel\Sanctum\HasApiTokens;


class AuthController extends Controller
{
    //register mensaje 201
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
        ]);

         $request->merge([
            'state' => $request->state ?? 'active',
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->state = $request->state;
        $user->save();

        return response($user, HttpFoundationResponse::HTTP_CREATED);
    }
    //login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        $credentials = request(['email', 'password']);

        if (Auth::attempt($credentials)) {
            /** @var \App\Models\User */
            $user = Auth::user();
            $token = $user->createToken('token')->plainTextToken;
            $cookie = cookie('cookie_token', $token, 60 * 24); // 1 day
            return response()->json(["token" => $token], HttpFoundationResponse::HTTP_OK)->withCookie($cookie);
        } else {
            return response()->json(["message" => "Credenciales inválidas"], HttpFoundationResponse::HTTP_UNAUTHORIZED);
        }
    }
    //logout
    public function logout(Request $request)
    {
        $cokkie = Cookie::forget('cookie_token');

        return response()->json([
            'message' => 'Sesión cerrada correctamente!'
        ]);
    }
    //userprofile
    public function userProfile(Request $request)
    {
        return response()->json([
            'message' => "Ssuario logueado",
            'userData' => $request->user(),
        ], HttpFoundationResponse::HTTP_OK);
    }
    //allusers
    public function allUsers()
    {
        return response()->json(User::all());
    }
}
