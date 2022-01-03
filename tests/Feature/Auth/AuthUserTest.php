<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthUserTest extends TestCase
{
  use RefreshDatabase;

  /** @test */
  public function ensure_user_can_authenticate()
  {
    Sanctum::actingAs(
      User::factory()->create(),
      ['*']
    );

    $response = $this->get('/api/user');


    $response->assertOk();
  }
}
