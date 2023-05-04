<?php

namespace Tests\Feature\Role;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Helpers\Datas;
use Tests\TestCase;

class RoleTest extends TestCase {

    use RefreshDatabase, Datas;
    /**
     * A basic feature test example.
     */
    public function testRolesAreCreatedCorrectly(): void {
        $role = Role::factory()->create($this->roleData('super-admin'));

        $user = User::factory()->create($this->userData($role->id));
        $token = $user->createToken('smsApiToken')->plainTextToken;

        $headers = ['Authorization' => "Bearer $token"];

        $payload = [
            'title' => 'admin',
            "description" => 'admin role',
            'is_active' => 1
        ];

        $response = $this->json('post', '/api/v1/roles', $payload, $headers);

        $response->assertStatus(201);
    }
}
