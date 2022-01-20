<?php

namespace App\Http\Controllers;

use App\Actions\App\Bet\StoreBetAction;
use App\Http\Requests\BetRequest;
use App\Http\Requests\StoreBetRequest;
use App\Http\Requests\UpdateBetRequest;
use App\Models\Bet;
use App\Models\BetableMatch;
use Illuminate\Http\Request;
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
  public function store(BetableMatch $match, StoreBetRequest $request, StoreBetAction $bet_action)
  {
    $bet = $bet_action->handle($match, $request);    

    return response()->json(
      $bet,
      Response::HTTP_CREATED,
      [],
      JSON_PRESERVE_ZERO_FRACTION
    );
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(
    UpdateBetRequest $request,
    BetableMatch $match,
    Bet $bet
  ) {
    $bet->odd = $request->odd;
    $bet->amount = $request->amount;
    $bet->save();

    return $bet;
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy(BetableMatch $match, Bet $bet)
  {
    $bet->delete();

    return ["result" => "Bet deleted."];
  }
}
