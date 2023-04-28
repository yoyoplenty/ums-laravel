<?php

namespace App\Http\Trait;

use Ramsey\Uuid\Uuid;
use Illuminate\Support\Str;

trait IdentityTrait {

    /**
     * Gererate UUID
     * 
     * @return string
     */
    protected function generateIdentity() {
        return (string)Uuid::uuid5(Uuid::uuid4(), Str::random(10));
    }

    protected function verifyTimeDiff($createdAt) {
        $verifyGenerated = strtotime($createdAt);
        $verifyAt = strtotime('now - 24 hours');

        if ($verifyAt <= $verifyGenerated) {
            return true;
        }
        return false;
    }

    /**
     * Gererate verification code
     * 
     * @return integer
     */
    protected function generateVerificationCode() {
        $code = mt_rand(1000000, 9999999);

        return $code;
    }
}
