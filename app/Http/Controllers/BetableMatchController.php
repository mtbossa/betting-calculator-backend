<?php

namespace App\Http\Controllers;

use App\Http\Requests\BetableMatchRequest;
use App\Models\Bet;
use App\Models\BetableMatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class BetableMatchController extends Controller
{
  public function index(Request $request)
  {
    if ($request->with_bets) {
      return BetableMatch::with("bets")
        ->where("user_id", Auth::user()->id)
        ->get();
    }

    return BetableMatch::where("user_id", Auth::user()->id)->get();
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
    if ($match->user_id !== $request->user()->id) {
      return response()->json(
        ["message" => "Match not found."],
        Response::HTTP_NOT_FOUND
      );
    }

    if ($request->with_bets) {
      $match = $match->load(['bets' => function ($query) {
        $query->orderBy('created_at', 'desc');
      }]);

      return response()->json(
        $match->toArray(),
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
    if ($match->user_id !== $request->user()->id) {
      return response()->json(
        ["message" => "Match not found."],
        Response::HTTP_NOT_FOUND
      );
    }

    $match->team_one = $request->team_one;
    $match->team_two = $request->team_two;
    $match->save();

    return $match;
  }

  public function destroy(BetableMatch $match, Request $request)
  {
    if ($match->user_id !== $request->user()->id) {
      return response()->json(
        ["message" => "Match not found."],
        Response::HTTP_NOT_FOUND
      );
    }

    $match->delete();

    return response(["message" => "Match deleted."]);
  }
}
