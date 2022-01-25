<?php

namespace App\Models;

use App\Scopes\NewestFirstScope;
use App\Traits\Multitenantable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BetableMatch
 *
 * @property int $id
 * @property string $team_one
 * @property string $team_two
 * @property int|null $winner_team
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Bet[] $bets
 * @property-read int|null $bets_count
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\BetableMatchFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|BetableMatch newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BetableMatch newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BetableMatch query()
 * @method static \Illuminate\Database\Eloquent\Builder|BetableMatch whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BetableMatch whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BetableMatch whereTeamOne($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BetableMatch whereTeamTwo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BetableMatch whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BetableMatch whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BetableMatch whereWinnerTeam($value)
 * @mixin \Eloquent
 */
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
