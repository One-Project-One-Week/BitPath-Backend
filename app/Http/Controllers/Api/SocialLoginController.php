<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Pest\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\userResource;
use Laravel\Socialite\Facades\Socialite;
use Google_Client;

class SocialLoginController extends Controller
{
    public function socialLogin(Request $request, $provider)
    {
        if ($provider !== 'google') {
            return response()->json(['error' => 'Unsupported provider'], 400);
        }

        $idToken = $request->input('token');
        
        // Set up the Google Client
        $client = new Google_Client(['client_id' => config('services.google.client_id')]);
        
        try {
            // Verify the ID token
            $payload = $client->verifyIdToken($idToken);
            
            if ($payload) {
                // Token is valid
                $googleId = $payload['sub'];
                $email = $payload['email'];
                $name = $payload['name'] ?? null;
                
                // Find or create user in your database
                $user = User::updateOrCreate(
                    ['provider_id' => $googleId],
                    [
                        'name' => $name,
                        'provider' => $provider,
                        'provider_id' => $googleId,
                        'provider_token' => $idToken,
                        'email' => $email,
                    ]
                );
                
                // Generate authentication token
                $token = $user->createToken('google-token')->plainTextToken;
                
                return response()->json([
                    'message' => "user logged in successfully",
                    'statusCode' => 200,
                    'data' => [
                        'user' => new userResource($user),
                        'token' => $token
                    ]
                ], 200);

            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 401);
        }
        
    }

}
