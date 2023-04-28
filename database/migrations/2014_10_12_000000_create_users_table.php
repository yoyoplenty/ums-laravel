<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->string('firstname');
            $table->string('lastname');
            $table->string('middlename')->nullable();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('phone_number', 15)->nullable();
            $table->string('profile_url')->nullable();
            $table->string('verification_code')->nullable();
            $table->dateTime('verification_code_generated_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->boolean('verified')->default(false);
            $table->foreignId('role_id');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('users');
    }
};
