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
    $this->withoutExceptionHandling();
    $response = $this->postJson('/register', [
      'name' => 'mateus',
      'email' => 'test@test.com',
      'password' => 'password',
      'password_confirmation' => 'password'
    ]);

    $this->assertDatabaseCount('users', 1);
  }

  /** @test */
  public function all_fields_are_required_when_register_user()
  {
    $response = $this->postJson('/register', []);

    $response->assertJsonValidationErrors(['name', 'email', 'password']);
  }

  /** @test */
  public function password_confirmation_is_required_and_must_match()
  {
    $response = $this->postJson('/register', [
      'name' => 'mateus',
      'email' => 'teste@teste.com',
      'password' => 'password',
    ]);

    $response->assertJsonValidationErrors(['password']);
  }

  /** @test */
  public function email_must_be_unique()
  {
    User::factory()->create(['email' => 'teste@teste.com']);

    $response = $this->postJson('/register', [
      'name' => 'mateus',
      'email' => 'teste@teste.com',
      'password' => 'password',
      'password_confirm' => 'password'
    ]);

    $response->assertJsonValidationErrors(['email' => 'The email has already been taken.']);
  }

  /** @test */
  public function password_must_be_greater_than_8_characters()
  {
    $response = $this->postJson('/register', [
      'name' => 'mateus',
      'email' => 'teste@teste.com',
      'password' => 'pass',
      'password_confirm' => 'pass'
    ]);

    $response->assertJsonValidationErrors(['password' => 'The password must be at least 8 characters.']);
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
