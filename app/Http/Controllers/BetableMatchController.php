<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBetableMatchRequest;
use App\Http\Requests\UpdateBetableMatchRequest;
use App\Models\BetableMatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class BetableMatchController extends Controller
{
  public function index(Request $request)
  {
    $query = BetableMatch::query();

    $query->when($request->boolean('with_bets'), function ($q) {
      $q->with('bets');
    });
    if($request->has('match_finished')) {
      $match_finished = $request->boolean('match_finished');
      $query->when($match_finished, function ($q) {
        $q->where('winner_team', '!=', null );
      });
      $query->when(!$match_finished, function ($q) {
        $q->where('winner_team', null);
      });
    }

    $matches = $query->get();

    return response()->json($matches, 200, [], JSON_PRESERVE_ZERO_FRACTION);
  }

  public function store(StoreBetableMatchRequest $request)
  {
    $match = BetableMatch::create([
      "team_one" => $request->team_one,
      "team_two" => $request->team_two,
      "winner_team" => null,
    ]);

    return $match;
  }

  public function show(BetableMatch $match, Request $request)
  {
    if ($request->with_bets) {
      $match = $match->load("bets");
    }

    return response()->json(
      $match->toArray(),
      200,
      [],
      JSON_PRESERVE_ZERO_FRACTION
    );
  }

  public function update(
    BetableMatch $match,
    UpdateBetableMatchRequest $request
  ) {
    $match->update($request->all());

    return $match;
  }

  public function destroy(BetableMatch $match, Request $request)
  {
    $match->delete();

    return response(["message" => "Match deleted."]);
  }
}
