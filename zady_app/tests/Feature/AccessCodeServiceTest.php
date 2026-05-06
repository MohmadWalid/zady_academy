<?php

namespace Tests\Feature;

use App\Services\AccessCodeService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccessCodeServiceTest extends TestCase
{
    use RefreshDatabase;

    protected AccessCodeService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new AccessCodeService();
    }

    public function test_generates_zady_format()
    {
        $code = $this->service->generate();
        
        $this->assertMatchesRegularExpression('/^ZADY-[A-Z0-9]{4}$/', $code);
    }

    public function test_code_is_unique()
    {
        // Actually, without mocking we just generate a few and check they are different.
        $code1 = $this->service->generate();
        $code2 = $this->service->generate();
        
        $this->assertNotEquals($code1, $code2);
    }

    public function test_throws_after_5_collisions()
    {
        // If we want to simulate 5 collisions, we would need to mock the str() helper or UUID generation,
        // or mock the DB to always say it exists.
        
        // Let's mock the 'generateUniqueCode' method logic by binding a mock or testing the service directly.
        // For simplicity, we can mock Str::uuid.
        \Illuminate\Support\Str::createUuidsUsing(function () {
            return \Ramsey\Uuid\Uuid::fromString('1234abcd-1234-1234-1234-123456789abc'); // will always generate ZADY-1234
        });

        // Create the first user with the code
        User::factory()->create(['access_code' => 'ZADY-1234']);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('فشل توليد كود الدخول بعد 5 محاولات. يرجى التواصل مع الدعم الفني.');

        $this->service->generate();

        // reset
        \Illuminate\Support\Str::createUuidsNormally();
    }
}
