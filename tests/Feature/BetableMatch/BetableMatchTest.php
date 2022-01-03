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
            User::factory()->create(),
            ['*']
        );

        $response = $this->post('/api/matches', [
            'team_one' => 'Pain',
            'team_two' => 'INTZ',
        ]);

        $this->assertDatabaseCount('matches', 1);

        $response->assertStatus(200);
    }

    /** @test */
    public function check_if_betable_match_can_be_deleted()
    {
        // $this->withoutExceptionHandling();

        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );

        $match = BetableMatch::create(['team_one' => 'Pain', 'team_two' => 'INTZ']);

        $this->delete("/api/matches/$match->id");

        $this->assertDeleted($match);
    }
}
