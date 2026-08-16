<?php

namespace App\Services;

class UploadTokenService
{
    /**
     * Generate a cryptographically secure random upload token.
     */
    public static function generateToken(): string
    {
        return bin2hex(random_bytes(20)); // 40-character unguessable hex string
    }
}
