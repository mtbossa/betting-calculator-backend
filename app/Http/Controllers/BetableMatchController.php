<?php

namespace App\Http\Controllers;

use App\Http\Requests\BetableMatchRequest;
use App\Models\Bet;
use App\Models\BetableMatch;
use Illuminate\Http\Request;

class BetableMatchController extends Controller
{
  public function index(Request $request)
  {
    if ($request->with_bets) {
      return BetableMatch::with("bets")->get();
    }

    return BetableMatch::all();
  }

  public function store(BetableMatchRequest $request)
  {
    $match = $request
      ->user()
      ->matches()
      ->create([
        "team_one" => $request->team_one,
        "team_two" => $request->team_two,
      ]);

    return $match;
  }

  public function show(BetableMatch $match, Request $request)
  {
    if ($request->with_bets) {
      return response()->json(
        $match->load("bets")->toArray(),
        200,
        [],
        JSON_PRESERVE_ZERO_FRACTION
      );
    }

    return response()->json(
      $match->toArray(),
      200,
      [],
      JSON_PRESERVE_ZERO_FRACTION
    );
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

    return response(["message" => "Match deleted."]);
  }
}
