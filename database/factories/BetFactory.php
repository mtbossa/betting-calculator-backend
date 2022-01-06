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
        $min = 0;
        $max = 100;
        $decimals = 2;

        $divisor = pow(10, $decimals);
        return [
            'winner_team' => mt_rand(1, 2),
            'odd' => mt_rand($min, $max * $divisor) / $divisor,
            'amount' => mt_rand(1, 10000),
            'currency' => 'BRL',
        ];
    }
}
