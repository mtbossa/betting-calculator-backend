<?php

namespace Tests\Feature\BetableMatch\Validations;

use App\Models\Bet;
use App\Models\BetableMatch;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BetableMatchValidationTest extends TestCase
{
  use RefreshDatabase, WithFaker;

  private \App\Models\User $user;

  public function setUp(): void
  {
    parent::setUp();

    $this->user = Sanctum::actingAs(User::factory()->create(), ["*"]);
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
  }

  /** @test */
  public function team_name_cant_be_greater_then_20_char()
  {
    // Creating match
    $this->postJson(route("betable_matches.store"), [
      "team_one" => $this->faker->sentence(19),
      "team_two" => $this->faker->sentence(19),
    ])->assertJsonValidationErrors([
      "team_one" => "The team one must not be greater than 20 characters.",
      "team_two" => "The team two must not be greater than 20 characters.",
    ]);

    // Updating match
    $match = BetableMatch::factory()->create(["user_id" => $this->user->id]);
    $update_values = [
      "team_one" => $this->faker->sentence(19),
      "team_two" => $this->faker->sentence(19),
    ];

    $this->putJson(
      route("betable_matches.update", $match->id),
      $update_values
    )->assertJsonValidationErrors([
      "team_one" => "The team one must not be greater than 20 characters.",
      "team_two" => "The team two must not be greater than 20 characters.",
    ]);
  }

  /** @test */
  public function ensure_winner_team_is_either_int_one_or_two()
  {
    $match = BetableMatch::factory()->create(["user_id" => $this->user->id]);

    $update_values = [
      "winner_team" => mt_rand(3, 10),
    ];

    $this->putJson(route("betable_matches.update", $match->id), $update_values)
      ->assertUnprocessable()
      ->assertJsonValidationErrorFor("winner_team");

    $this->assertDatabaseMissing("matches", $update_values);
  }
}
