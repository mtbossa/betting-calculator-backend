<?php

namespace App\Http\Controllers;

use App\Models\BetableMatch;
use Illuminate\Http\Request;

class BetableMatchController extends Controller
{
    public function index(Request $request)
    {
        return BetableMatch::all();
    }

    public function store(Request $request)
    {
        $match = $request->user()->matches()->create([
            'team_one' => $request->team_one,
            'team_two' => $request->team_two,
        ]);

        return $match;
    }

    public function show(BetableMatch $match)
    {
        return response()->json($match->toArray(), 200, [], JSON_PRESERVE_ZERO_FRACTION);
    }

    public function destroy(BetableMatch $match)
    {
        $match->delete();
    }
}
