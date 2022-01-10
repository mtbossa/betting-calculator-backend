<?php

namespace App\Http\Controllers;

use App\Http\Requests\BetableMatchRequest;
use App\Models\BetableMatch;
use Illuminate\Http\Request;

class BetableMatchController extends Controller
{
    public function index()
    {
        return BetableMatch::all();
    }

    public function store(BetableMatchRequest $request)
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

    public function update(BetableMatch $match, BetableMatchRequest $request)
    {
        $match->team_one = $request->team_one;
        $match->team_two = $request->team_two;
        $match->save();

        return $match;
    }

    public function destroy(BetableMatch $match)
    {
        $match->delete();
    }
}
