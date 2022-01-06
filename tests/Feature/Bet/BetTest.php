<?php

namespace Tests\Feature\Bet;

use App\Models\Bet;
use App\Models\BetableMatch;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BetTest extends TestCase
{
  use RefreshDatabase;

  /** @test */
  public function check_if_bet_can_be_created()
  {
    Sanctum::actingAs(
      $user = User::factory()->create(),
      ['*']
    );

    $match = BetableMatch::factory()->create(['user_id' => $user->id]);

    $response = $this->postJson("api/matches/$match->id/bets", [
      'winner_team' => 1,
      'odd' => 1.5,
      'amount' => 25, // Reais
      'currency' => 'BRL'
    ]);

    $this->assertDatabaseCount('bets', 1);
    $this->assertDatabaseHas('bets', [
      'winner_team' => 1,
      'match_id' => $match->id,
    ]);

    $response->assertStatus(201);
    $response->assertJson(Bet::first()->toArray());
  }

  /** @test */
  public function check_if_bet_can_be_deleted()
  {
    $this->withoutExceptionHandling();

    Sanctum::actingAs(
      $user = User::factory()->create(),
      ['*']
    );

    $match = $user->matches()->create([
      'team_one' => 'INTZ',
      'team_two' => 'Pain',
    ]);

    $bet = $match->bets()->create([
      'winner_team' => 1,
      'odd' => 1.5,
      'amount' => 25,
      'currency' => 'BRL',
    ]);

    $this->delete("/api/matches/$match->id/bets/$bet->id");

    $this->assertDeleted($bet);
  }
}
