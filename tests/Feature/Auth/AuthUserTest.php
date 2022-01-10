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

  /** @test */
  public function ensure_unauthenticated_user_cannot_get_responses()
  {
    $response = $this->getJson('/api/user');

    $response->assertJson(['message' => 'Unauthenticated.']);
    $response->assertStatus(401);
  }

  /** @test */
  public function user_can_register()
  {
    $response = $this->postJson('/register', [
      'name' => 'mateus',
      'email' => 'test@test.com',
      'password' => 'password',
      'password_confirmation' => 'password'
    ]);

    $this->assertDatabaseCount('users', 1);
  }

  /** @test */
  public function invalid_credentials_returns_error()
  {
    User::factory()->create();
    $response = $this->postJson('/login', ['email' => 'a@a.com', 'password' => 'a']);
    // Even if email exists but password is wrong, the error goes in the 'email' field.
    
    $response->assertJson(['message' => 'The given data was invalid.']);
    $response->assertJsonStructure(['message', 'errors']);
    $response->assertJsonValidationErrorFor('email');
  }

  /** @test */
  public function email_and_password_are_required()
  {
    $user = User::factory()->create();

    $response = $this->postJson('/login', []);

    $response->assertJsonValidationErrors(['email', 'password']);
  }
}
