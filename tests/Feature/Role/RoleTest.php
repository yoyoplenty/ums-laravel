<?php

namespace Tests\Feature\Role;

use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Helpers\AuthUser;
use Tests\Helpers\Datas;
use Tests\TestCase;

class RoleTest extends TestCase {

    use RefreshDatabase, AuthUser, Datas;

    /**
     * A basic feature test example.
     */

    public function testRolesAreCreatedCorrectly(): void {
        $headers = $this->userHeader();

        $payload = [
            'title' => 'admin',
            "description" => 'admin role',
            'is_active' => 1
        ];

        $response = $this->json('post', '/api/v1/roles', $payload, $headers);

        $response->assertStatus(201)
            ->assertJsonPath('data.title', 'admin')
            ->assertJsonPath('data.description', 'admin role');
    }

    public function testRolesThrowErrorWithoutValidData(): void {
        $headers = $this->userHeader();

        $payload = [
            "description" => 'admin role',
            'is_active' => 1
        ];

        $response = $this->json('post', '/api/v1/roles', $payload, $headers);

        $response->assertStatus(422);
    }

    public function testRolesThrowErrorWithoutAValidAdminUser(): void {
        $headers = $this->userHeader('user');

        $payload = [
            'title' => 'admin',
            "description" => 'admin role',
            'is_active' => 1
        ];

        $response = $this->json('post', '/api/v1/roles', $payload, $headers);

        $response->assertStatus(403)
            ->assertJsonPath('message', 'Unauthorized');
    }

    public function testGetAllRoles(): void {
        $user = $this->authUser();

        $response = $this->actingAs($user)
            ->get('/api/v1/roles');

        $response->assertStatus(200);
    }

    public function testGetRolesThrowErrorWithoutAValidAdminUser(): void {
        $user = $this->authUser('user');

        $response = $this->actingAs($user)
            ->get('/api/v1/roles');

        $response->assertStatus(403)
            ->assertJsonPath('message', 'Unauthorized');
    }

    public function testGetASingleRole(): void {
        $user = $this->authUser();

        $role = Role::factory()->create($this->roleData('student'));

        $response = $this->actingAs($user)
            ->get('/api/v1/roles/' . $role->id);

        $response->assertStatus(200)
            ->assertJsonPath('data.title', 'student');
    }

    public function testGetSingleRoleThrowsErrorWithInvalidId(): void {
        $user = $this->authUser();

        $roleId = rand(50, 60);

        $response = $this->actingAs($user)
            ->get('/api/v1/roles/' . $roleId);

        $response->assertStatus(422)
            ->assertJsonPath('message', 'unable to fetch role');
    }
}
