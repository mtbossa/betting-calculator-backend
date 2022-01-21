<?php

namespace App\Models;

use App\Scopes\NewestFirstScope;
use App\Traits\Multitenantable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BetableMatch extends Model
{
  use HasFactory, Multitenantable;

  protected $fillable = ["team_one", "team_two", "winner_team"];

  protected $table = "matches";

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function bets()
  {
    return $this->hasMany(Bet::class, "match_id");
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
