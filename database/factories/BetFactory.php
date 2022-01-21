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
        $profit = round($odd * $amount, 2);
        $real_profit  = round($profit - $amount, 2);
        
        return [
            'betted_team' => mt_rand(1, 2),
            'odd' => $odd,
            'amount' => $amount,
            'profit' => $profit,
            'real_profit' => $real_profit,
        ];
    }
}
