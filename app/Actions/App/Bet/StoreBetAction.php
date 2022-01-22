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
    $odd = (float) number_format($request->odd, 2);
    $amount = (float) number_format($request->amount, 2);

    $profit = $this->calcProfit($odd, $amount);
    $real_profit = $this->calcRealProfit($profit, $amount);

    return $match->bets()->create([
      "betted_team" => $request->betted_team,
      "odd" => $odd,
      "amount" => $amount,
      "profit" => $profit,
      "real_profit" => $real_profit,
    ]);
  }

  private function calcProfit(float $odd, float $amount): float
  {
    return $odd * $amount;
  }

  private function calcRealProfit(float $profit, float $amount): float
  {
    return $profit - $amount;
  }
}
