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

  private $user;

  public function setUp(): void
  {
    parent::setUp();

    $this->user = Sanctum::actingAs(
      User::factory()->create(),
      ['*']
    );
  }

  /** @test */
  public function check_if_bet_can_be_created()
  {
    $match = BetableMatch::factory()->create(['user_id' => $this->user->id]);

    $this->postJson(route('matches.bets.store', [
      'match' => $match->id,
      'winner_team' => 1,
      'odd' => 1.5,
      'amount' => 25, // Reais
      'currency' => 'BRL'
    ]))->assertStatus(201)
      ->assertJson(Bet::first()->toArray());

    $this->assertDatabaseCount('bets', 1);
    $this->assertDatabaseHas('bets', [
      'winner_team' => 1,
      'match_id' => $match->id,
    ]);
  }

  /** @test */
  public function check_if_bet_can_be_updated()
  {
    $match = BetableMatch::factory()->create(['user_id' => $this->user->id]);

    $bet = Bet::factory()->create(['match_id' => $match->id]);

    $update_values = [
      'odd' => 2,
      'amount' => 30,
    ];

    $this->putJson(route('matches.bets.update', ['match' => $match->id, 'bet' => $bet->id]), $update_values)
      ->assertJsonFragment($update_values);

    $this->assertDatabaseHas('bets', $update_values);
  }

  /** @test */
  public function check_if_bet_can_be_deleted()
  {
    $match = BetableMatch::factory()->create(['user_id' => $this->user->id]);
    $bet = Bet::factory()->create(['match_id' => $match->id]);

    $this->delete(route('matches.bets.destroy', ['match' => $match->id, 'bet' => $bet->id]));

    $this->assertDeleted($bet);
    $this->assertDatabaseHas('matches', ['id' => $match->id]);
  }

  /** @test */
  public function odd_winner_team_and_amount_are_required_on_creation()
  {
    $match = BetableMatch::factory()->create(['user_id' => $this->user->id]);

    // Creating bet
    $this->postJson(route('matches.bets.store', [
      'match' => $match->id,
      'winner_team' => '',
      'odd' => '',
      'amount' => ''
    ]))->assertJsonValidationErrors([
      'winner_team' => 'The winner team field is required.',
      'odd' => 'The odd field is required.',
      'amount' => 'The amount field is required.',
    ]);
  }

  /** @test */
  public function winner_team_and_currency_are_not_required_when_updating()
  {
    $match = BetableMatch::factory()->create(['user_id' => $this->user->id]);

    $bet = Bet::factory()->create(['match_id' => $match->id]);

    $this->putJson(route('matches.bets.update', ['match' => $match->id, 'bet' => $bet->id]), [
      'odd' => '',
      'amount' => '',
    ])->assertJsonMissingValidationErrors(['winner_team'])
      ->assertJsonValidationErrors(['odd', 'amount']);
  }
}
