<?php

namespace Tests\Feature\BetableMatch;

use App\Models\Bet;
use App\Models\BetableMatch;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BetableMatchTest extends TestCase
{
  use RefreshDatabase, WithFaker;

  private $user;

  public function setUp(): void
  {
    parent::setUp();

    $this->user = Sanctum::actingAs(User::factory()->create(), ["*"]);
  }

  /** @test */
  public function check_if_betable_match_can_be_created()
  {
    $match = BetableMatch::factory()->make();
    $this->postJson(route("betable_matches.store"), $match->toArray())
      ->assertCreated()
      ->assertJson($match->toArray());

    $this->assertDatabaseCount("matches", 1);
    $this->assertDatabaseHas("matches", [
      "user_id" => $this->user->id,
    ]);
  }

  /** @test */
  public function fetch_all_betable_matches_and_only_users_matches_are_returned()
  {
    $matches = BetableMatch::factory(2)->create(["user_id" => $this->user->id]);

    $user_2 = User::factory()->create();
    BetableMatch::factory(2)->create(["user_id" => $user_2->id]);

    $this->getJson(route("betable_matches.index"))
      ->assertJsonCount(2)
      ->assertJsonMissingExact(["user_id" => $user_2->id])
      ->assertJson($matches->toArray());
  }

  /** @test */
  public function fetch_single_betable_match()
  {
    $match = BetableMatch::factory()->create(["user_id" => $this->user->id]);

    $this->getJson(route("betable_matches.show", $match->id))->assertJson(
      $match->toArray()
    );
  }

  /** @test */
  public function ensure_user_can_fetch_only_his_match()
  {
    // User 1 Match
    BetableMatch::factory()->create(["user_id" => $this->user->id]);

    // User 2 Match
    $user_2 = User::factory()->create();
    $match_2 = BetableMatch::factory()->create(["user_id" => $user_2->id]);

    // User 1 tries to get User 2 Match
    $this->getJson(route("betable_matches.show", $match_2->id))
      ->assertNotFound()
      ->assertJson(["message" => "Match not found."]);
  }

  /** @test */
  public function ensure_fetched_matches_have_bets_when_requested()
  {
    $this->withoutExceptionHandling();

    $match = BetableMatch::factory()->create(["user_id" => $this->user->id]);
    Bet::factory(10)->create(["match_id" => $match->id]);

    // Fetch single match
    $this->getJson(
      route("betable_matches.show", [$match->id, "with_bets=true"])
    )
      ->assertJson($match->toArray())
      ->assertJsonPath("bets.0.odd", Bet::first()->odd);

    // Fetch all matches
    $this->getJson(route("betable_matches.index", ["with_bets=true"]))
      ->assertJson([0 => $match->toArray()])
      ->assertJsonPath("0.bets.0.odd", Bet::first()->odd);
  }

  /** @test */
  public function both_team_names_are_required()
  {
    // Creating match
    $this->postJson(route("betable_matches.store"), [])
      ->assertUnprocessable()
      ->assertJsonValidationErrors([
        "team_one" => "The team one field is required.",
        "team_two" => "The team two field is required.",
      ]);

    // Updating match
    $match = BetableMatch::factory()->create(["user_id" => $this->user->id]);
    $update_values = [
      "team_one" => "",
      "team_two" => "",
    ];

    $this->putJson(route("betable_matches.update", $match->id), $update_values)
      ->assertUnprocessable()
      ->assertJsonValidationErrors([
        "team_one" => "The team one field is required.",
        "team_two" => "The team two field is required.",
      ]);
  }

  /** @test */
  public function team_name_cant_be_greater_then_50_char()
  {
    // Creating match
    $this->postJson(route("betable_matches.store"), [
      "team_one" => $this->faker->sentence(49),
      "team_two" => $this->faker->sentence(49),
    ])->assertJsonValidationErrors([
      "team_one" => "The team one must not be greater than 50 characters.",
      "team_two" => "The team two must not be greater than 50 characters.",
    ]);

    // Updating match
    $match = BetableMatch::factory()->create(["user_id" => $this->user->id]);
    $update_values = [
      "team_one" => $this->faker->sentence(49),
      "team_two" => $this->faker->sentence(49),
    ];

    $this->putJson(
      route("betable_matches.update", $match->id),
      $update_values
    )->assertJsonValidationErrors([
      "team_one" => "The team one must not be greater than 50 characters.",
      "team_two" => "The team two must not be greater than 50 characters.",
    ]);
  }

  /** @test */
  public function check_if_betable_match_can_be_updated()
  {
    $match = BetableMatch::factory()->create(["user_id" => $this->user->id]);

    $update_values = ["team_one" => "INTZ", "team_two" => "Pain"];

    $this->putJson(route("betable_matches.update", $match->id), $update_values)
      ->assertOk()
      ->assertJsonFragment($update_values);
    $this->assertDatabaseHas("matches", $update_values);
  }

  /** @test */
  public function ensure_user_can_updated_only_his_match()
  {
    // User 1 Match
    BetableMatch::factory()->create(["user_id" => $this->user->id]);

    // User 2 Match
    $user_2 = User::factory()->create();
    $match_2 = BetableMatch::factory()->create(["user_id" => $user_2->id]);

    $update_values = ["team_one" => "INTZ", "team_two" => "Pain"];

    $this->putJson(
      route("betable_matches.update", $match_2->id),
      $update_values
    )
      ->assertNotFound()
      ->assertJson(["message" => "Match not found."]);
    $this->assertDatabaseMissing("matches", $update_values);
  }

  /** @test */
  public function check_if_betable_match_with_bets_can_be_deleted()
  {
    $match = BetableMatch::factory()->create(["user_id" => $this->user->id]);
    Bet::factory()->create(["match_id" => $match->id]);

    $this->delete(route("betable_matches.destroy", $match->id));
    $this->assertDatabaseMissing("bets", ["match_id" => $match->id]);
    $this->assertDeleted($match);
  }

  /** @test */
  public function ensure_user_can_delete_only_his_match()
  {
    // User 1 Match
    BetableMatch::factory()->create(["user_id" => $this->user->id]);

    // User 2 Match
    $user_2 = User::factory()->create();
    $match_2 = BetableMatch::factory()->create(["user_id" => $user_2->id]);

    $this->delete(route("betable_matches.destroy", $match_2->id))
      ->assertNotFound()
      ->assertJson(["message" => "Match not found."]);
    $this->assertDatabaseHas("matches", ["id" => $match_2->id]);
  }

  /** @test */
  public function ensure_correct_response_is_returned_when_betable_match_not_found()
  {
    $this->withoutExceptionHandling();
    $this->get(route("betable_matches.show", 1))->assertJson([
      "message" => "Match not found.",
    ]);
  }
}
