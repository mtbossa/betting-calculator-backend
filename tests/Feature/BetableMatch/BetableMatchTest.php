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

    /** @test */
    public function check_if_betable_match_can_be_created()
    {
        Sanctum::actingAs(
            $user = User::factory()->create(),
            ['*']
        );

        $response = $this->postJson(route('betable_matches.store'), [
            'team_one' => 'Pain',
            'team_two' => 'INTZ',
        ]);

        $this->assertDatabaseCount('matches', 1);
        $this->assertDatabaseHas('matches', [
            'user_id' => $user->id,
        ]);

        $response->assertStatus(201);
        $response->assertJson([
            'team_one' => 'Pain',
            'team_two' => 'INTZ',
        ]);
    }

    /** @test */
    public function fetch_all_betable_matches()
    {
        Sanctum::actingAs(
            $user = User::factory()->create(),
            ['*']
        );

        $matches = BetableMatch::factory(2)->create(['user_id' => $user->id]);

        $response = $this->getJson(route('betable_matches.index'));

        $response->assertJsonCount(2);
        $response->assertJson($matches->toArray());
    }

    /** @test */
    public function fetch_single_betable_match_with_bets()
    {
        Sanctum::actingAs(
            $user = User::factory()->create(),
            ['*']
        );

        $match = BetableMatch::factory()->create(['user_id' => $user->id]);
        $bets = Bet::factory(10)->create(['match_id' => $match->id]);

        $response = $this->getJson(route('betable_matches.show', $match->id));
        
        $response->assertJson($match->toArray());
        $response->assertJsonPath('bets.0.odd', Bet::first()->odd);
    }

    /** @test */
    public function ensure_fetched_matches_always_have_bets()
    {
        Sanctum::actingAs(
            $user = User::factory()->create(),
            ['*']
        );

        $match = BetableMatch::factory()->create(['user_id' => $user->id]);
        $bets = Bet::factory(10)->create(['match_id' => $match->id]);

        // Fetch single match
        $response = $this->getJson(route('betable_matches.show', $match->id));        
        $response->assertJson($match->toArray());
        $response->assertJsonPath('bets.0.odd', Bet::first()->odd);        

        // Fetch all matches
        $response = $this->getJson(route('betable_matches.index'));
        $response->assertJson([0 => $match->toArray()]);
        $response->assertJsonPath('0.bets.0.odd', Bet::first()->odd);
    }

    /** @test */
    public function both_team_names_are_required() 
    {
        Sanctum::actingAs(
            $user = User::factory()->create(),
            ['*']
        );

        $response = $this->postJson(route('betable_matches.store'), []);

        $response->assertJsonValidationErrors(['team_one' => 'The team one field is required.', 'team_two' => 'The team two field is required.']);
    }

    /** @test */
    public function team_name_cant_be_greater_then_50_char() 
    {
        Sanctum::actingAs(
            $user = User::factory()->create(),
            ['*']
        );

        $response = $this->postJson(route('betable_matches.store'), [
            'team_one' => $this->faker->sentence(49),
            'team_two' => $this->faker->sentence(49)
        ]);

        $response->assertJsonValidationErrors([
            'team_one' => 'The team one must not be greater than 50 characters.',
             'team_two' => 'The team two must not be greater than 50 characters.'
        ]);
    }

    /** @test */
    public function check_if_betable_match_can_be_deleted()
    {
        Sanctum::actingAs(
            $user = User::factory()->create(),
            ['*']
        );

        $match = $user->matches()->create([
            'team_one' => 'INTZ',
            'team_two' => 'Pain',
        ]);

        $this->delete(route('betable_matches.destroy', $match->id));

        $this->assertDeleted($match);
    }
}
