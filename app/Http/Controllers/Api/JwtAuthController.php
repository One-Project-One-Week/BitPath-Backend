<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\userResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class JwtAuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:7',
        ]);

        if($validator->fails()){
            return response()->json([
                'statusCode' => 400,
                'errors' => $validator->errors(),
            ], 400);
        }

        $refresh_token = Str::random(60);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'refresh_token' => hash('sha256', $refresh_token),
            'password' => Hash::make($request->password),
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'statusCode' => 201,
            'message' => 'User registered successfully',
            'data' => [
                'user' => new userResource($user),
                'access_token' => $token,
                'expires_in' => config('jwt.ttl') * 60,
            ]
        ], 201)->cookie('refresh_token', $refresh_token, 60 * 24 * 7, null, null, true, true);
    }



    public function login(Request $request)
    {
        $cred = $request->only('email', 'password');

        try{
            if(! $token = JWTAuth::attempt($cred)){
                return response()->json([
                    'statusCode' => 401,
                    'message' => 'Invalid credentials',
                ], 401);
            }
        }catch(JWTException $e){
            return response()->json([
                'statusCode' => 500,
                'message' => 'could not create token',
            ], 500);
        }

        $user = JWTAuth::user();

        $refresh_token = Str::random(60);
        $user->update([
            'refresh_token' => hash('sha256', $refresh_token),
        ]);

        return response()->json([
            'statusCode' => 200,
            'message' => 'User logged in successfully',
            'data' => [
                'user' => new userResource($user),
                'access_token' => $token,
                'expires_in' => config('jwt.ttl') * 60,
            ]
            ])->cookie('refresh_token', $refresh_token , 60 * 24 * 7, null, null, true, true);

    }

    public function profile(){
        $user = JWTAuth::user();

        return response()->json([
            'statusCode' => 200,
            'message' => 'User profile retrieved successfully',
            'data' => [
                'user' => $user,
            ]
        ], 200);
    }

    public function logout(){
        JWTAuth::invalidate(JWTAuth::getToken());
        return response()->json([
            'statusCode' => 200,
            'message' => 'User logged out successfully',
        ], 200);
    }


    public function refresh(Request $request){

        $refresh_token = $request->cookie('refresh_token');
        $hashed_refresh_token = hash('sha256', $refresh_token);
        $user = User::where('refresh_token', $hashed_refresh_token)->first();

        if(!$user){
            return response()->json([
                'statusCode' => 401,
                'message' => 'Invalid refresh token',
            ], 401);
        }
        
        $new_access_token = JWTAuth::fromUser($user);

        return response()->json([
            'statusCode' => 200,
            'message' => 'Access token refreshed successfully',
            'data' => [
                'access_token' => $new_access_token,
            ]
        ]);
    }
}
