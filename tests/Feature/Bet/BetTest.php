<?php

namespace Tests\Feature\Bet;

use App\Models\Bet;
use App\Models\BetableMatch;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Testing\Fluent\AssertableJson;
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
      ]),
      ["betted_team" => 1, "odd" => 1.5, "amount" => 25.0]
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
  public function betted_team_is_not_required_when_updating()
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

  /** @test */
  public function when_receive_odd_or_amount_with_more_than_two_decimals_convert_to_two_and_rounded_up()
  {
    $match = BetableMatch::factory()->create(["user_id" => $this->user->id]);

    $this->postJson(
      route("matches.bets.store", [
        "match" => $match->id,
      ]),
      ["betted_team" => 1, "odd" => 1.5590004, "amount" => 25.10005]
    )->assertJson([
      "betted_team" => 1,
      "odd" => 1.56,
      "amount" => 25.1,
    ]);
  }

  /** @test */
  public function ensure_betted_team_is_either_int_one_or_two()
  {
    $match = BetableMatch::factory()->create(["user_id" => $this->user->id]);

    $this->postJson(
      route("matches.bets.store", [
        "match" => $match->id,
      ]),
      ["betted_team" => 3]
    )->assertJsonValidationErrors("betted_team");
  }

  /** @test */
  public function ensure_odd_and_amount_are_numeric()
  {
    $match = BetableMatch::factory()->create(["user_id" => $this->user->id]);

    $this->postJson(
      route("matches.bets.store", [
        "match" => $match->id,
      ]),
      ["betted_team" => 1, "odd" => "12a", "amount" => "aaa43.5"]
    )->assertJsonValidationErrors(["odd", "amount"]);
  }

  /** @test */
  public function ensure_odd_and_amount_digits_are_between_1_and_10()
  {
    $match = BetableMatch::factory()->create(["user_id" => $this->user->id]);

    $this->postJson(
      route("matches.bets.store", [
        "match" => $match->id,
      ]),
      ["betted_team" => 1, "odd" => 12345678910, "amount" => 12345678910]
    )->assertJsonValidationErrors([
      "odd" => "The odd must be between 1 and 10 digits.",
      "amount" => "The amount must be between 1 and 10 digits.",
    ]);

    $bet = Bet::factory()->create([
      "match_id" => $match->id,
      "user_id" => $this->user->id,
    ]);

    $this->putJson(
      route("bets.update", [
        "bet" => $bet->id,
      ]),
      ["odd" => 12345678910, "amount" => 12345678910]
    )->assertJsonValidationErrors([
      "odd" => "The odd must be between 1 and 10 digits.",
      "amount" => "The amount must be between 1 and 10 digits.",
    ]);
  }

  /** @test */
  public function odd_and_amount_are_not_required_when_updating()
  {
    $match = BetableMatch::factory()->create(["user_id" => $this->user->id]);

    $bet = Bet::factory()->create([
      "match_id" => $match->id,
      "user_id" => $this->user->id,
    ]);

    $this->putJson(
      route("bets.update", [
        "bet" => $bet->id,
      ]),
      []
    )->assertJsonMissingValidationErrors(["odd", "amount"]);
  }
}
