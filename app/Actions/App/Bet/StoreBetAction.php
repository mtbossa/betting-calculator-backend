<?php

namespace App\Actions\App\Bet;

use App\Http\Requests\StoreBetRequest;
use App\Models\Bet;
use App\Models\BetableMatch;
use Illuminate\Support\Facades\Auth;

class StoreBetAction
{
  public function handle(BetableMatch $match, StoreBetRequest $request): Bet
  {
    $profit = $request->odd * $request->amount;
    $real_profit = $profit - $request->amount;

    return $match->bets()->create([
      "betted_team" => $request->betted_team,
      "odd" => $request->odd,
      "amount" => $request->amount,
      "profit" => $profit,
      "real_profit" => $real_profit,
      "user_id" => Auth::user()->id,
    ]);
  }
}
