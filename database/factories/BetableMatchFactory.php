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
            'team_one' => $this->faker->company,
            'team_two' => $this->faker->company,
        ];
    }
}
