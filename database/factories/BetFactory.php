<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class BetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'winner_team' => mt_rand(1, 2),
            'odd' => mt_rand(1, 5),
            'amount' => mt_rand(1, 10000),
            'currency' => 'BRL',
        ];
    }
}
