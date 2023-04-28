<?php

namespace Database\Seeders;

use App\Http\Trait\IdentityTrait;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class UserSeeder extends Seeder {

    use IdentityTrait;

    /**
     * Run the database seeds.
     */
    public function run(): void {
        DB::table('users')->truncate();

        /*** GET ROLES */

        $role_super_admin = Role::where('slug', 'super-admin')->first();

        $role_user  = Role::where('slug', 'user')->first();


        /*** SEED NEW USERS */

        $superAdmin = new User();
        $superAdmin->uuid = $this->generateIdentity();
        $superAdmin->firstname = 'Adebiyi';
        $superAdmin->lastname = 'Blessing';
        $superAdmin->middlename = 'Olubunmi';
        $superAdmin->email = 'yoyoplenty@gmail.com';
        $superAdmin->password = 'Password123';
        $superAdmin->role_id = $role_super_admin->id;
        $superAdmin->verification_code = $this->generateVerificationCode();
        $superAdmin->verification_code_generated_at = now();
        $superAdmin->verified_at = now();
        $superAdmin->verified = true;
        $superAdmin->remember_token = Str::random(10);
        $superAdmin->save();

        $user = new User();
        $user->uuid = $this->generateIdentity();
        $user->firstname = 'John';
        $user->lastname = 'Doe';
        $user->email = 'yoyo@gmail.com';
        $user->password = 'Password123';
        $user->role_id = $role_user->id;
        $user->verification_code = $this->generateVerificationCode();
        $user->verification_code_generated_at = now();
        $user->verified_at = now();
        $user->verified = true;
        $user->remember_token = Str::random(10);
        $user->save();
    }
}
