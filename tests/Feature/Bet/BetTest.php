<?php

namespace Tests\Feature\Bet;

use App\Models\Bet;
use App\Models\BetableMatch;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BetTest extends TestCase
{
  use RefreshDatabase;

  private $user;

  public function setUp(): void
  {
    parent::setUp();
    $this->user = Sanctum::actingAs(User::factory()->create(), ["*"]);
  }

  /** @test */
  public function check_if_bet_can_be_created()
  {
    $match = BetableMatch::factory()->create(["user_id" => $this->user->id]);

    $response = $this->postJson(
      route("matches.bets.store", [
        "match" => $match->id,
        "betted_team" => 1,
        "odd" => 1.5,
        "amount" => 25.0,
      ])
    )
      ->assertCreated()
      ->assertJson(
        array_merge(Bet::first()->toArray(), [
          "profit" => 37.5,
          "real_profit" => 12.5,
        ])
      );

    $this->assertDatabaseCount("bets", 1);
    $this->assertDatabaseHas("bets", [
      "betted_team" => 1,
      "profit" => 37.5,
      "real_profit" => 12.5,
      "match_id" => $match->id,
    ]);
  }

  /** @test */
  public function check_if_bet_can_be_updated()
  {
    $match = BetableMatch::factory()->create(["user_id" => $this->user->id]);
    $bet = Bet::factory()->create([
      "match_id" => $match->id,
      "user_id" => $this->user->id,
    ]);

    $update_values = [
      "odd" => 2,
      "amount" => 30,
    ];

    $this->putJson(route("bets.update", ["bet" => $bet->id]), $update_values)
      ->assertOk()
      ->assertJsonFragment($update_values);

    $this->assertDatabaseHas("bets", $update_values);
  }

  /** @test */
  public function ensure_user_can_update_only_his_bet()
  {
    Event::fake(); // Need for creating model with other user_id

    $match = BetableMatch::factory()->create(["user_id" => $this->user->id]);
    Bet::factory()->create([
      "match_id" => $match->id,
      "user_id" => $this->user->id,
    ]);

    $user_2 = User::factory()->create();
    $match_2 = BetableMatch::factory()->create(["user_id" => $user_2->id]);

    $bet_2 = Bet::factory()->create([
      "match_id" => $match_2->id,
      "user_id" => $user_2->id,
    ]);

    $update_values = [
      "odd" => 2,
      "amount" => 30,
    ];

    $this->putJson(route("bets.update", ["bet" => $bet_2->id]), $update_values)
      ->assertNotFound()
      ->assertJson(["message" => "Bet not found."]);

    $this->assertDatabaseMissing("bets", $update_values);
  }

  /** @test */
  public function check_if_bet_can_be_deleted()
  {
    $match = BetableMatch::factory()->create(["user_id" => $this->user->id]);
    $bet = Bet::factory()->create([
      "match_id" => $match->id,
      "user_id" => $this->user->id,
    ]);

    $this->delete(route("bets.destroy", ["bet" => $bet->id]))
      ->assertOk()
      ->assertJson(["message" => "Bet deleted."]);

    $this->assertDeleted($bet);
    $this->assertDatabaseHas("matches", ["id" => $match->id]);
  }

  /** @test */
  public function ensure_user_can_delete_only_his_bet()
  {
    Event::fake();

    $match = BetableMatch::factory()->create(["user_id" => $this->user->id]);
    Bet::factory()->create([
      "match_id" => $match->id,
      "user_id" => $this->user->id,
    ]);

    $user_2 = User::factory()->create();
    $match_2 = BetableMatch::factory()->create(["user_id" => $user_2->id]);
    $bet_2 = Bet::factory()->create([
      "match_id" => $match_2->id,
      "user_id" => $user_2->id,
    ]);

    $this->delete(route("bets.destroy", ["bet" => $bet_2->id]))
      ->assertNotFound()
      ->assertJson(["message" => "Bet not found."]);

    $this->assertDatabaseHas("bets", ["id" => $bet_2->id]);
  }

  /** @test */
  public function odd_betted_team_and_amount_are_required_on_creation()
  {
    $match = BetableMatch::factory()->create(["user_id" => $this->user->id]);

    // Creating bet
    $this->postJson(
      route("matches.bets.store", [
        "match" => $match->id,
        "betted_team" => "",
        "odd" => "",
        "amount" => "",
      ])
    )->assertJsonValidationErrors([
      "betted_team" => "The betted team field is required.",
      "odd" => "The odd field is required.",
      "amount" => "The amount field is required.",
    ]);
  }

  /** @test */
  public function betted_team_and_currency_are_not_required_when_updating()
  {
    $match = BetableMatch::factory()->create(["user_id" => $this->user->id]);

    $bet = Bet::factory()->create([
      "match_id" => $match->id,
      "user_id" => $this->user->id,
    ]);

    $this->putJson(route("bets.update", ["bet" => $bet->id]), [
      "odd" => "",
      "amount" => "",
    ])
      ->assertJsonMissingValidationErrors(["betted_team"])
      ->assertJsonValidationErrors(["odd", "amount"]);
  }

  /** @test */
  public function ensure_correct_response_is_returned_when_bet_not_found()
  {
    $this->delete(route("bets.destroy", 1))->assertJson([
      "message" => "Bet not found.",
    ]);

    $this->put(route("bets.update", 1))->assertJson([
      "message" => "Bet not found.",
    ]);
  }
}
