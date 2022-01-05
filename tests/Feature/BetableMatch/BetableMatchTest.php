<?php

namespace Tests\Feature\BetableMatch;

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
        $this->withoutExceptionHandling();

        Sanctum::actingAs(
            $user = User::factory()->create(),
            ['*']
        );

        $matches = BetableMatch::factory(2)->create(['user_id' => $user->id]);

        $response = $this->getJson(route('betable_matches.index'));

        $response->assertJsonCount(2);
        $response->assertJson($matches->toArray());
    }

        $match = $user->matches()->createMany($form_data);

        $response = $this->get(route('betable_matches.index'));

        $response->assertJsonCount(2);
        $response->assertJson($form_data);
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
