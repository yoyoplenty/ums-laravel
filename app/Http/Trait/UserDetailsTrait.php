<?php

namespace App\Http\Trait;

use Ramsey\Uuid\Uuid;
use Illuminate\Support\Str;

trait UserDetailsTrait {

    use IdentityTrait;

    /**
     * Gererate UUID
     * 
     * @return string
     */
    protected function generateUserDetails(array $payload): array {
        // Generating user details....
        $payload['uuid'] = $this->generateIdentity();
        $payload['verification_code'] = $this->generateVerificationCode();
        $payload['verification_code_generated_at'] = now();
        $payload['password'] = data_get($payload, 'password') ?: 'Password123';

        return $payload;
    }
}
