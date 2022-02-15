<?php

namespace App\Services;

use App\Http\Requests\StoreBetableMatchRequest;
use App\Http\Requests\UpdateBetableMatchRequest;
use App\Models\BetableMatch;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class BetableMatchService {
  public function index(Request $request): Collection
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

    return $query->get();
  }
}
