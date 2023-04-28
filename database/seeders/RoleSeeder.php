<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void {
        DB::table('roles')->truncate();

        $super_admin = new Role(['title' => 'Super Admin', 'description' => 'Super Administrator', 'is_active' => 1]);
        $super_admin->save();

        $admin = new Role(['title' => 'Admin', 'description' => 'Administrator', 'is_active' => 1]);
        $admin->save();

        $user = new Role(['title' => 'User', 'description' => 'User', 'is_active' => 1]);
        $user->save();
    }
}
