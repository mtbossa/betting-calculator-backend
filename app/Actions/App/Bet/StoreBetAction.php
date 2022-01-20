<?php

namespace App\Actions\App\Bet;

use App\Http\Requests\StoreBetRequest;
use App\Models\Bet;
use App\Models\BetableMatch;

class StoreBetAction
{
  public function handle(BetableMatch $match, StoreBetRequest $request): Bet
  {
    $profit = $request->odd * $request->amount;
    $real_profit = $profit - $request->amount;

    return $match->bets()->create([
      "winner_team" => $request->winner_team,
      "odd" => $request->odd,
      "amount" => $request->amount,
      "profit" => $profit,
      "real_profit" => $real_profit,
    ]);
  }
}
