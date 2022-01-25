<?php

namespace App\Models;

use App\Scopes\NewestFirstScope;
use App\Traits\Multitenantable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Bet
 *
 * @property int $id
 * @property int $betted_team
 * @property float $odd
 * @property float $amount
 * @property float $profit
 * @property float $real_profit
 * @property int $match_id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\BetableMatch $match
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\BetFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Bet newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Bet newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Bet query()
 * @method static \Illuminate\Database\Eloquent\Builder|Bet whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bet whereBettedTeam($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bet whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bet whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bet whereMatchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bet whereOdd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bet whereProfit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bet whereRealProfit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bet whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bet whereUserId($value)
 * @mixin \Eloquent
 */
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
