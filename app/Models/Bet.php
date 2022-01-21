<?php

namespace App\Models;

use App\Scopes\NewestFirstScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bet extends Model
{
  use HasFactory;

  protected $fillable = [
    "winner_team",
    "odd",
    "amount",
    "profit",
    "real_profit",
  ];

  protected $casts = [
    "winner_team" => "int",
    "odd" => "float",
    "amount" => "float",
    "profit" => "float",
    "real_profit" => "float",
  ];

  public function match()
  {
    return $this->belongsTo(BetableMatch::class, "match_id");
  }

  /**
   * The "booted" method of the model.
   *
   * @return void
   */
  protected static function booted()
  {
    static::addGlobalScope(new NewestFirstScope());
  }
}
