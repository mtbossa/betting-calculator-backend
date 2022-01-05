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
    use RefreshDatabase;

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
        // $this->withoutExceptionHandling();
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
    public function check_if_betable_match_can_be_deleted()
    {
        // $this->withoutExceptionHandling();

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
