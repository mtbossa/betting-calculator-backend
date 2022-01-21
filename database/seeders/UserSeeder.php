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
    // Default User
    User::factory()
      ->default()
      ->create()
      ->each(function ($user) {
        BetableMatch::factory(5)
          ->sequence(["winner_team" => null], ["winner_team" => mt_rand(1, 2)])
          ->create(["user_id" => $user->id])
          ->each(function ($match) {
            Bet::factory(6)->create([
              "match_id" => $match->id,
              "user_id" => $match->user_id,
            ]);
          });
      });

    User::factory(5)
      ->create()
      ->each(function ($user) {
        BetableMatch::factory(5)
          ->sequence(["winner_team" => null], ["winner_team" => mt_rand(1, 2)])
          ->create(["user_id" => $user->id])
          ->each(function ($match) {
            Bet::factory(6)->create([
              "match_id" => $match->id,
              "user_id" => $match->user_id,
            ]);
          });
      });
  }
}
