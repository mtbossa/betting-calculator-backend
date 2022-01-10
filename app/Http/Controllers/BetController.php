<?php

namespace App\Http\Controllers;

use App\Http\Requests\BetRequest;
use App\Models\Bet;
use App\Models\BetableMatch;
use Illuminate\Http\Request;

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
    public function store(BetableMatch $match, BetRequest $request)
    {  
        $bet = $match->bets()->create([
            'winner_team' => $request->winner_team,
            'odd' => $request->odd,
            'amount' => $request->amount,
            'currency' => $request->currency,
        ]);
        
        return $bet;
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
    public function update(BetRequest $request, BetableMatch $match, Bet $bet)
    {
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

        return ['result' => 'Bet deleted.'];
    }
}
