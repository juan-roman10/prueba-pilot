<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Helper para autenticar al usuario con token JWT en los headers.
     */
    protected function authenticateAs(User $user)
    {
        $token = JWTAuth::fromUser($user);
        return $this->withHeader('Authorization', 'Bearer ' . $token);
    }

    /* -------------------------------------------------------------------------- */
    /*                                REGISTRO                                    */
    /* -------------------------------------------------------------------------- */

    #[Test]
    public function user_can_register_successfully(): void
    {
        $payload = [
            'name'     => 'Mariano Test',
            'email'    => 'mariano@example.com',
            'password' => 'password123',
            'rol'      => 'editor',
            'estado'   => 'activo',
        ];

        $response = $this->postJson('/api/auth/register', $payload);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'message',
                     'user' => ['id', 'name', 'email', 'rol', 'estado'],
                     'token',
                 ]);

        $this->assertDatabaseHas('users', [
            'email'  => 'mariano@example.com',
            'rol'    => 'editor',
            'estado' => 'activo',
        ]);
    }

    #[Test]
    public function user_registration_applies_default_rol_and_estado(): void
    {
        $payload = [
            'name'     => 'Juan Perez',
            'email'    => 'juan@example.com',
            'password' => 'password123',
        ];

        $response = $this->postJson('/api/auth/register', $payload);

        $response->assertStatus(201);

        $this->assertDatabaseHas('users', [
            'email'  => 'juan@example.com',
            'rol'    => 'editor',
            'estado' => 'activo',
        ]);
    }

    #[Test]
    public function cannot_register_with_existing_email(): void
    {
        User::factory()->create(['email' => 'duplicate@example.com']);

        $response = $this->postJson('/api/auth/register', [
            'name'     => 'Otro Usuario',
            'email'    => 'duplicate@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email']);
    }

    #[Test]
    public function cannot_register_with_short_password(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name'     => 'Usuario',
            'email'    => 'usuario@example.com',
            'password' => '12345', // menos de 6 caracteres
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['password']);
    }

    /* -------------------------------------------------------------------------- */
    /*                                  LOGIN                                     */
    /* -------------------------------------------------------------------------- */

    #[Test]
    public function active_user_can_login_with_correct_credentials(): void
    {
        $user = User::factory()->create([
            'email'    => 'active@example.com',
            'password' => Hash::make('password123'),
            'estado'   => 'activo',
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email'    => 'active@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'access_token',
                     'token_type',
                     'expires_in',
                 ]);
    }

    #[Test]
    public function cannot_login_with_incorrect_password(): void
    {
        User::factory()->create([
            'email'    => 'user@example.com',
            'password' => Hash::make('password123'),
            'estado'   => 'activo',
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email'    => 'user@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
                 ->assertJson([
                     'error' => 'Credenciales inválidas o usuario inactivo',
                 ]);
    }

    #[Test]
    public function inactive_user_cannot_login(): void
    {
        User::factory()->create([
            'email'    => 'inactive@example.com',
            'password' => Hash::make('password123'),
            'estado'   => 'inactivo', // Usuario inactivo
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email'    => 'inactive@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(401)
                 ->assertJson([
                     'error' => 'Credenciales inválidas o usuario inactivo',
                 ]);
    }

    /* -------------------------------------------------------------------------- */
    /*                                   ME                                       */
    /* -------------------------------------------------------------------------- */

    #[Test]
    public function authenticated_user_can_get_own_profile(): void
    {
        $user = User::factory()->create([
            'name'   => 'Mariano Profile',
            'email'  => 'profile@example.com',
            'estado' => 'activo',
        ]);

        $response = $this->authenticateAs($user)->getJson('/api/auth/me');

        $response->assertStatus(200)
                 ->assertJson([
                     'id'    => $user->id,
                     'name'  => 'Mariano Profile',
                     'email' => 'profile@example.com',
                 ]);
    }

    #[Test]
    public function unauthenticated_user_cannot_get_profile(): void
    {
        $response = $this->getJson('/api/auth/me');

        $response->assertStatus(401);
    }

    /* -------------------------------------------------------------------------- */
    /*                                  REFRESH                                   */
    /* -------------------------------------------------------------------------- */

    #[Test]
    public function authenticated_user_can_refresh_token(): void
    {
        $user = User::factory()->create(['estado' => 'activo']);

        $response = $this->authenticateAs($user)->postJson('/api/auth/refresh');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'access_token',
                     'token_type',
                     'expires_in',
                 ]);
    }

    /* -------------------------------------------------------------------------- */
    /*                                  LOGOUT                                    */
    /* -------------------------------------------------------------------------- */

    #[Test]
    public function authenticated_user_can_logout(): void
    {
        $user = User::factory()->create(['estado' => 'activo']);

        $response = $this->authenticateAs($user)->postJson('/api/auth/logout');

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Sesión cerrada correctamente',
                 ]);
    }
}
