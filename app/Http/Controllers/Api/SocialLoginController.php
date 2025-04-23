<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Pest\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\userResource;
use Laravel\Socialite\Facades\Socialite;

class SocialLoginController extends Controller
{
    public function socialLogin(Request $request, $provider){
        $token = $request->input('token');
        $user = Socialite::driver($provider)->stateless()->userFromToken($token);

        $refresh_token = Str::random(60);
    
        $user = User::firstOrCreate([
            'email' => $user->getEmail(),
        ],[
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'provider' => $provider,
            'provider_id' => $user->getId(),
            'provider_token' => $user->token,
            'refresh_token' => $refresh_token,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'statusCode' => 201,
            'message' => 'User logged in successfully',
            'data' => [
                'user' => new userResource($user),
                'access_token' => $token,
                'expires_in' => config('jwt.ttl') * 60,
            ]
            ], 201 )->cookie('refresh_token', $refresh_token, 60 * 24 * 7, null, null, true, true);
    }
}
