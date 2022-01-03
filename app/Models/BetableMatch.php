<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BetableMatch extends Model
{
    use HasFactory;

    protected $fillable = ['team_one', 'team_two'];

    protected $table = 'matches';
}
