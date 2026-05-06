<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Str;
use RuntimeException;

class AccessCodeService
{
    /**
     * Generate a unique ZADY-XXXX access code.
     * Tries up to 5 times before throwing.
     */
    public function generate(): string
    {
        for ($attempt = 1; $attempt <= 5; $attempt++) {
            $code = $this->buildCode();

            if (! User::withTrashed()->where('access_code', $code)->exists()) {
                return $code;
            }
        }

        throw new RuntimeException(
            'فشل توليد كود الدخول بعد 5 محاولات. يرجى التواصل مع الدعم الفني.'
        );
    }

    /**
     * Build a single ZADY-XXXX candidate from a UUID4.
     * Strip non-alphanumeric chars, uppercase, take first 4 chars.
     */
    private function buildCode(): string
    {
        $uuid     = Str::uuid()->toString();
        $stripped = strtoupper(preg_replace('/[^A-Z0-9]/i', '', $uuid));

        return 'ZADY-' . substr($stripped, 0, 4);
    }
}
