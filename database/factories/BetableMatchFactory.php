<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class BetableMatchFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array
   */
  public function definition()
  {
    return [
      "team_one" => $this->faker->company,
      "team_two" => $this->faker->company,
      "winner_team" => null,
    ];
  }

  /**
   * Not finished match.
   *
   * @return \Illuminate\Database\Eloquent\Factories\Factory
   */
  public function finished()
  {
    return $this->state(function (array $attributes) {
      return [
        "winner_team" => $this->faker->boolean() ? mt_rand(1, 2) : null,
      ];
    });
  }
}
