<?php

namespace Tests\Feature\Auth;

use App\Models\Password;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Helpers\AuthUser;
use Tests\Helpers\Datas;
use Tests\TestCase;

class AuthTest extends TestCase {

    use RefreshDatabase, AuthUser, Datas;

    /**
     * A basic feature test example.
     */

    public function testLoginUserSuccessfuly(): void {
        $user = $this->authUser();

        $loginPayload = [
            "email" => $user->email,
            "password" => 'Password123',
            'role_id' => $user->role_id,
        ];

        $response = $this->postJson('/api/v1/login', $loginPayload);

        $response->assertStatus(200);
    }

    public function testThrowsErrorWithInvaidEmail(): void {
        $user = $this->authUser();

        $loginPayload = [
            "email" => fake()->unique()->safeEmail(),
            "password" => 'Password123',
            'role_id' => $user->role_id,
        ];

        $response = $this->postJson('/api/v1/login', $loginPayload);

        $response->assertStatus(422)
            ->assertJsonPath('message', 'The selected email is invalid.');
    }

    public function testThrowsErrorWithInvaidPassword(): void {
        $user = $this->authUser();

        $loginPayload = [
            "email" => $user->email,
            "password" => $user->password,
            'role_id' => $user->role_id,
        ];

        $response = $this->postJson('/api/v1/login', $loginPayload);

        $response->assertStatus(422)
            ->assertJsonPath('error', 'invalid password provided');
    }

    public function testThrowsErrorWithUnverifiedUser(): void {
        $user = $this->authUser('user', false);

        $loginPayload = [
            "email" => $user->email,
            "password" => 'Password123',
            'role_id' => $user->role_id,
        ];

        $response = $this->postJson('/api/v1/login', $loginPayload);

        $response->assertStatus(422)
            ->assertJsonPath('error', 'Please check your email to verify your account!!!');
    }

    public function testRegisterUserSuccessfully(): void {
        $role = Role::factory()->create($this->roleData('user'));

        $userPayload = [
            'firstname' => 'John',
            'lastname' => 'Doe',
            'email' => 'yoyo@gmail.com',
            'uuid' => fake()->uuid(),
            'password' => 'Password123',
            'role_id' => $role->id,
        ];

        $response = $this->postJson('/api/v1/register', $userPayload);

        $response->assertStatus(201)
            ->assertJsonPath('message', 'successfully registered user');
    }

    public function testRegisterUserThrowsErrorWithInvalidEmail(): void {
        $role = Role::factory()->create($this->roleData('user'));

        $userPayload = [
            'firstname' => 'John',
            'lastname' => 'Doe',
            'email' => fake()->unique()->safeEmail(),
            'uuid' => fake()->uuid(),
            'password' => 'Password123',
            'role_id' => $role->id,
        ];

        $response = $this->postJson('/api/v1/register', $userPayload);

        $response->assertStatus(422);
    }

    public function testVerifyEmailSuccessfuly(): void {
        $user = $this->authUser('user', false);

        $response = $this->getJson('/api/v1/' . $user->email . '/verify/' . $user->verification_code);

        $response->assertStatus(200)
            ->assertJsonPath('message', 'successfully verified user');
    }

    public function testVerifyThrowErrorIfUserIsVerified(): void {
        $user = $this->authUser();

        $response = $this->getJson('/api/v1/' . $user->email . '/verify/' . $user->verification_code);

        $response->assertStatus(422)
            ->assertJsonPath('error', 'User already verified');
    }

    public function testResendVerificationEmailSuccessfuly(): void {
        $user = $this->authUser('user', false);

        $response = $this->getJson('/api/v1/' . $user->email . '/resend_verification');

        $response->assertStatus(200)
            ->assertJsonPath('message', 'successfully resent email');
    }


    public function testResendVerificationEmailThrowsErrorWithVerifiedUser(): void {
        $user = $this->authUser();

        $response = $this->getJson('/api/v1/' . $user->email . '/resend_verification');

        $response->assertStatus(422)
            ->assertJsonPath('error', 'User already verified');
    }

    public function testResetPasswordSuccessfuly(): void {
        $user = $this->authUser();

        $payload = [
            'email' => $user->email,
            'token' =>  mt_rand(1000000, 9999999),
            'created_at' => now()
        ];

        Password::factory()->create($payload);
        $passwordPayload = ['password' => 'Password@1'];

        $response = $this->patchJson('/api/v1/' . $user->email . '/reset_password', $passwordPayload);

        $response->assertStatus(200)
            ->assertJsonPath('message', 'Password reset successful');
    }

    public function testResetPasswordThrowsErrorWithNoResetData(): void {
        $user = $this->authUser();

        $passwordPayload = ['password' => 'Password@1'];

        $response = $this->patchJson('/api/v1/' . $user->email . '/reset_password', $passwordPayload);

        $response->assertStatus(422)
            ->assertJsonPath('error', 'unable to fetch Data');
    }


    public function testLogoutSuccessfuly(): void {
        $user = $this->authUser();

        $response = $this->actingAs($user)
            ->post('/api/v1/logout');

        $response->assertStatus(200)
            ->assertJsonPath('message', 'Logout Successful');
    }
}
