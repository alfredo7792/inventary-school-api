<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Facades\JWTFactory;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    public function loginAdmin()
    {
        $credentials = request(['email', 'password']);

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'Unauthorised'], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => $e], 500);
        }

        $user = JWTAuth::user();
        $payload = JWTFactory::sub($user->id)
            ->uid($user->id)
            ->make();
        $token = JWTAuth::fromUser($user, $payload);

        return $this->respondWithToken($token, $user);
    }

    
    protected function respondWithToken($token, $user)
    {
        //$expiresIn = Auth::guard('api')->factory()->getTTL() * 60;

        return response()->json([
            'access_token' => $token,
            'user' => [
                'names' => $user->names,
                'surnames' => $user->surnames,
                'email' => $user->email,
                'user_created_at' => $user->user_created_at,
                'role' =>  $user->role->name,
            ],
        ]);
    }
}
