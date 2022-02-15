<?php

namespace App\Services;

use App\Models\BetableMatch;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class BetableMatchService {
  public function index(Request $request): \Illuminate\Support\Collection
  {
    $query = $this->_filterMatchesQuery($request);

    return $query->get();
  }

  private function _filterMatchesQuery(Request $request): Builder
  {
    $query = BetableMatch::query();

    $query->when($request->boolean('with_bets'), function ($q) {
      $q->with('bets');
    });

    $query->when($request->date('start_date'), function($q, $start_date) {
      $test = $start_date;
      $q->whereDate('created_at', '>=', $start_date);
    });

    $query->when($request->date('end_date'), function($q, $end_date) {
      $test = $end_date;
      $q->whereDate('created_at', '<=', $end_date);
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

    return $query;
  }
}
