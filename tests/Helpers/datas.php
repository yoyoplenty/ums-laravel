<?php

namespace Tests\Helpers;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;

trait Datas {
    /**
     * Generates user Datas
     */
    public function userData(int $roleId = 2, int $verification = 1) {
        return [
            'firstname' => fake()->name(),
            'lastname' => fake()->name(),
            'middlename' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'uuid' => fake()->uuid(),
            'password' => 'Password123',
            'verified' => $verification,
            'role_id' => $roleId,
            'verification_code' =>  mt_rand(1000000, 9999999),
            'verification_code_generated_at' => now()
        ];
    }

    /**
     * Creates role Datas
     */
    public function roleData(string $slug = 'user') {
        return [
            'title' => $slug,
            'description' => fake()->name(),
            'slug' => $slug,
            'is_active' => 1,
        ];
    }
}
