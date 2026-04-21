<?php

namespace App\Http\Controllers;

use App\Console\Commands\GenerateLeaderboard;
use App\Models\LeaderboardSnapshot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class LeaderboardController extends Controller
{
    public function index()
    {
        $top3        = LeaderboardSnapshot::with('user')->orderBy('rank')->limit(3)->get();
        $entries     = LeaderboardSnapshot::with('user')->orderBy('rank')->paginate(10);
        $generatedAt = LeaderboardSnapshot::latest('generated_at')->value('generated_at');
        $totalUsers  = LeaderboardSnapshot::count();
        $avgRate     = LeaderboardSnapshot::avg('completion_rate');

        return view('admin.leaderboard', compact('top3', 'entries', 'generatedAt', 'totalUsers', 'avgRate'));
    }

    public function generate()
    {
        Artisan::call(GenerateLeaderboard::class);
        return back()->with('success', 'Leaderboard generated successfully.');
    }
}
