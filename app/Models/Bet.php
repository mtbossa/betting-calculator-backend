<?php

namespace App\Models;

use App\Scopes\NewestFirstScope;
use App\Traits\Multitenantable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bet extends Model
{
  use HasFactory, Multitenantable;

  protected $fillable = [
    "betted_team",
    "odd",
    "amount",
    "profit",
    "real_profit",
    "user_id",
  ];

  protected $casts = [
    "betted_team" => "int",
    "odd" => "float",
    "amount" => "float",
    "profit" => "float",
    "real_profit" => "float",
  ];

  public function match()
  {
    return $this->belongsTo(BetableMatch::class, "match_id");
  }

  public function user()
  {
    return $this->belongsTo(User::class);
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
