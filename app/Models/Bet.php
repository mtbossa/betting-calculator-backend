<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bet extends Model
{
    use HasFactory;

    protected $fillable = ['winner_team', 'odd', 'amount', 'currency'];

    public function match()
    {
        return $this->belongsTo(BetableMatch::class, 'match_id');
    }
}
