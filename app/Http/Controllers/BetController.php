<?php

namespace App\Http\Controllers;

use App\Actions\App\Bet\StoreBetAction;
use App\Http\Requests\StoreBetRequest;
use App\Http\Requests\UpdateBetRequest;
use App\Models\Bet;
use App\Models\BetableMatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class BetController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    //
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(
    BetableMatch $match,
    StoreBetRequest $request,
    StoreBetAction $action
  ) {
    $bet = $action->handle($match, $request);

    return response()->json(
      $bet,
      Response::HTTP_CREATED,
      [],
      JSON_PRESERVE_ZERO_FRACTION
    );
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(UpdateBetRequest $request, Bet $bet)
  {
    if ($bet->match->user_id !== $request->user()->id) {
      return response()->json(
        ["message" => "Bet not found."],
        Response::HTTP_NOT_FOUND
      );
    }

    $bet->update($request->all());

    return $bet;
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy(Bet $bet, Request $request)
  {
    if ($bet->match->user_id !== $request->user()->id) {
      return response()->json(
        ["message" => "Bet not found."],
        Response::HTTP_NOT_FOUND
      );
    }

    $bet->delete();

    return ["message" => "Bet deleted."];
  }
}
