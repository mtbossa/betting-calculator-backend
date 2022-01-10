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

    $response = $this->postJson(route('matches.bets.store', [
      'match' => $match->id,
      'winner_team' => 1,
      'odd' => 1.5,
      'amount' => 25, // Reais
      'currency' => 'BRL'
    ]));

    $this->assertDatabaseCount('bets', 1);
    $this->assertDatabaseHas('bets', [
      'winner_team' => 1,
      'match_id' => $match->id,
    ]);

    $response->assertStatus(201);
    $response->assertJson(Bet::first()->toArray());
  }

  /** @test */
  public function check_if_bet_can_be_updated()
  {
    // $this->withoutExceptionHandling();

    Sanctum::actingAs(
      $user = User::factory()->create(),
      ['*']
    );

    $match = BetableMatch::factory()->create(['user_id' => $user->id]);

    $bet = Bet::factory()->create(['match_id' => $match->id]);

    $update_values = [
      'odd' => 2,
      'amount' => 30,
    ];

    $response = $this->putJson(route('matches.bets.update', [
      'match' => $match->id,
      'bet' => $bet->id,
      'odd' => 2,
      'amount' => 30,
    ]));

    $response->assertJsonFragment($update_values);
    $this->assertDatabaseHas('bets', $update_values);
  }

  /** @test */
  public function check_if_bet_can_be_deleted()
  {
    $this->withoutExceptionHandling();

    Sanctum::actingAs(
      $user = User::factory()->create(),
      ['*']
    );

    $match = BetableMatch::factory()->create(['user_id' => $user->id]);

    $bet = Bet::factory()->create(['match_id' => $match->id]);

    $this->delete(route('matches.bets.destroy', ['match' => $match->id, 'bet' => $bet->id]));

    $this->assertDeleted($bet);
  }

  /** @test */
  public function odd_winner_team_and_amount_are_required()
  {
    Sanctum::actingAs(
      $user = User::factory()->create(),
      ['*']
    );

    $match = BetableMatch::factory()->create(['user_id' => $user->id]);

    // Creating bet
    $response = $this->postJson(route('matches.bets.store', [
      'match' => $match->id,
      'winner_team' => '',
      'odd' => '',
      'amount' => ''
    ]));

    $response->assertJsonValidationErrors([
      'winner_team' => 'The winner team field is required.',
      'odd' => 'The odd field is required.',
      'amount' => 'The amount field is required.',
    ]);

    // Updating match
    $match = BetableMatch::factory()->create(['user_id' => $user->id]);
    $bet = Bet::factory()->create(['match_id' => $match->id]);

    $response = $this->putJson(route('matches.bets.update', [
      'match' => $match->id, 'winner_team' => '',
      'bet' => $bet->id,
      'winner_team' => '',
      'odd' => '',
      'amount' => ''
    ]));

    $response->assertJsonValidationErrors([
      'winner_team' => 'The winner team field is required.',
      'odd' => 'The odd field is required.',
      'amount' => 'The amount field is required.',
    ]);
  }
}
