<?php

namespace Tests\Helpers;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;

trait Datas {
    /**
     * Generates user Datas
     */
    public function userData(int $roleId = 2) {
        return [
            'firstname' => fake()->name(),
            'lastname' => fake()->name(),
            'middlename' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'uuid' => fake()->uuid(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
            'role_id' => $roleId,
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
