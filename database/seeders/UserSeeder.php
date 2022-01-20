<?php

namespace Database\Seeders;

use App\Models\Bet;
use App\Models\BetableMatch;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->default()->create()->each(function ($user) {
            BetableMatch::factory(10)->create(['user_id' => $user->id])->each(function ($match) {
                Bet::factory(20)->create(['match_id' => $match->id]);
            });
        });

        User::factory(10)->create();
    }
}
