<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Hash;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable {
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = ['email_verified_at' => 'datetime'];

    public function setPasswordAttribute($value) {
        $this->attributes['password'] = Hash::make($value);
    }

    public function isAdmin(): bool {
        $role_super_admin = Role::where('slug', 'super-admin')->first();

        if ($this->role_id === optional($role_super_admin)->id) return true;

        else return false;
    }

    public function hasRole($roleName): bool {
        $name = Str::ucfirst($roleName);
        $role = Role::where('slug',  $name)->first();

        if ($role && $this->role_id === $role->id) return true;

        else return false;
    }

    /**
     * A user has one role
     */
    public function role(): HasOne {
        return $this->hasOne(Role::class);
    }
}
