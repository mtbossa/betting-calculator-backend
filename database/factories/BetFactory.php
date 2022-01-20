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
        $min = 1;
        $max = 10;

        $odd = mt_rand($min * 10, $max * 10) / 10;
        $amount = mt_rand(5, 200);
        $profit = $odd * $amount;
        $real_profit  = $profit - $amount;
        
        return [
            'winner_team' => mt_rand(1, 2),
            'odd' => $odd,
            'amount' => $amount,
            'profit' => $profit,
            'real_profit' => $real_profit,
        ];
    }
}
