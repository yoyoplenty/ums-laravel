<?php

namespace Tests\Feature\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Helpers\AuthUser;
use Tests\Helpers\Datas;
use Tests\TestCase;

class UserTest extends TestCase {
    use RefreshDatabase, AuthUser, Datas;

    /**
     * A basic feature test example.
     */

    public function testGetAllUsersCorrectly(): void {
        $user = $this->authUser();

        $response = $this->actingAs($user)
            ->get('/api/v1/users');

        $response->assertStatus(200);
    }

    public function testGetUsersThrowErrorWithoutAValidAdminUser(): void {
        $user = $this->authUser('user');

        $response = $this->actingAs($user)
            ->get('/api/v1/users');

        $response->assertStatus(403)
            ->assertJsonPath('message', 'Unauthorized');
    }

    public function testGetASingleUserCorrectly(): void {
        $user = $this->authUser();

        $response = $this->actingAs($user)
            ->get('/api/v1/users/' . $user->uuid);

        $response->assertStatus(200);
    }

    public function testGetSingleUserThrowsErrorWithInvalidId(): void {
        $user = $this->authUser();

        $userId = rand(50, 60);

        $response = $this->actingAs($user)
            ->get('/api/v1/users/' . $userId);

        $response->assertStatus(422)
            ->assertJsonPath('message', 'unable to fetch user');
    }

    public function testUpdateAUserCorrectly(): void {
        $user = $this->authUser();

        $updatePayload = ['middlename' => 'Doe', 'uuid' => $user->uuid];

        $response = $this->actingAs($user)
            ->patch('/api/v1/users/' . $user->uuid, $updatePayload);

        $response->assertStatus(200)
            ->assertJsonPath('message', 'Successfully updated');
    }

    public function testDeleteARoleCorrectly(): void {
        $user = $this->authUser();

        $response = $this->actingAs($user)
            ->delete('/api/v1/users/' . $user->uuid);

        $response->assertStatus(200)
            ->assertJsonPath('message', 'User deleted successfully');
    }
}
