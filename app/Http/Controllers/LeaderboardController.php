<?php

namespace App\Http\Controllers;

use App\Http\Resources\LeaderboardResource;
use App\Models\User;
use Illuminate\Http\Request;

class LeaderboardController extends Controller
{
    public function index()
    {
        $users = User::with('roadmaps')
            ->orderBy('current_streak', 'desc')
            ->get();
        return response()->json([
            'status' => 200,
            'users' => LeaderboardResource::collection($users),
        ], 200);
    }
}
