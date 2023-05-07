<?php

namespace Tests\Helpers;

use App\Models\Role;
use App\Models\User;

trait AuthUser {
    /**
     * Generates user Datas
     */
    public function userHeader(string $roleSlug = 'super-admin') {
        $role = Role::factory()->create($this->roleData($roleSlug));

        $user = User::factory()->create($this->userData($role->id));
        $token = $user->createToken('smsApiToken')->plainTextToken;

        $headers = ['Authorization' => "Bearer $token"];

        return $headers;
    }

    public function authUser(string $roleSlug = 'super-admin'): User {
        $role = Role::factory()->create($this->roleData($roleSlug));

        $user = User::factory()->create($this->userData($role->id));

        return $user;
    }
}
