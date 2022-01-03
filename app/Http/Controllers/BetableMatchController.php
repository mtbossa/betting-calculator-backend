<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BetableMatchController extends Controller
{
    public function store(Request $request)
    {
        $match = $request->user()->matches()->create([
            'team_one' => $request->team_one,
            'team_two' => $request->team_two,
        ]);
    }
}
