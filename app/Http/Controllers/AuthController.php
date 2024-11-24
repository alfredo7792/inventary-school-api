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

        return $this->respondWithToken($token, $user, true, 200, "Login exitoso");
    }

    public function logout(Request $request)
    {
        try {
            $token = $request->bearerToken();

            if (!$token) {
                return $this->successOrErrorResponse(false, 401, 'Token no proporcionado', []);
            }
            JWTAuth::setToken($token)->invalidate();

            try {
                JWTAuth::setToken($token)->parseToken()->authenticate();
            } catch (JWTException $e) {
                return $this->successOrErrorResponse(true, 200, 'Logout exitoso', []);
            }

            return $this->successOrErrorResponse(false, 500, 'Token aún activo después de invalidación', []);

        } catch (JWTException $e) {
            return $this->successOrErrorResponse(false, 500, 'Error: ' . $e->getMessage(), []);
        }
    }
    
    protected function respondWithToken($token, $user, $success, $status, $message)
    {
        //$expiresIn = Auth::guard('api')->factory()->getTTL() * 60;

        $data= [
            'access_token' => $token,
            'user' => [
                'names' => $user->names,
                'surnames' => $user->surnames,
                'email' => $user->email,
                'user_created_at' => $user->user_created_at,
                'role' =>  $user->role->name,
            ],
        ];

        return response()->json([
            'success' => $success,
            'status' => $status,
            'message' => $message,
            'data' => $data,
        ]);
    }

    protected function successOrErrorResponse($success, $status, $message, $data)
    {
        return response()->json([
            'success' => $success,
            'status' => $status,
            'message' => $message,
            'data' => $data,
        ]);
    }
}
