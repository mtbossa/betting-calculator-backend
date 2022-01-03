<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BetableMatch extends Model
{
    use HasFactory;

    protected $fillable = ['team_one', 'team_two'];

    protected $table = 'matches';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bets()
    {
        return $this->hasMany(Bet::class, 'match_id');
    }
}
