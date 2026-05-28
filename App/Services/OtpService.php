<?php

// FILE: app/Services/OtpService.php
// ============================================================
namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class OtpService
{
    private const TTL_MINUTES = 10;
    private const CODE_LENGTH = 6;

    public function send(string $phone): string
    {
        $code = (string) random_int(
            (int) str_repeat('1', self::CODE_LENGTH),
            (int) str_repeat('9', self::CODE_LENGTH),
        );

        Cache::put("otp:{$phone}", $code, now()->addMinutes(self::TTL_MINUTES));

        // TODO: Integrate SMS provider (e.g. Vonage/Twilio/Vodafone SMS)
        \Log::info("OTP for {$phone}: {$code}");

        return $code;
    }

    public function verify(string $phone, string $code): bool
    {
        $cached = Cache::get("otp:{$phone}");
        if ($cached && $cached === $code) {
            Cache::forget("otp:{$phone}");
            return true;
        }
        return false;
    }
}

// ============================================================