<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_valid_code_logs_in_and_redirects_by_role()
    {
        $admin = User::factory()->create(['role' => 'admin', 'access_code' => 'ZADY-ADMN']);
        
        $response = $this->post('/login', ['access_code' => 'ZADY-ADMN']);
        
        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);
    }

    public function test_invalid_code_returns_arabic_error()
    {
        $response = $this->post('/login', ['access_code' => 'ZADY-INVAL']); // exactly 10 chars
        
        $response->assertSessionHasErrors(['access_code' => 'الكود غير صحيح، تواصل مع الإدارة']);
        $this->assertGuest();
    }

    public function test_soft_deleted_user_cannot_login()
    {
        $user = User::factory()->create(['role' => 'teacher', 'access_code' => 'ZADY-TEST']);
        $user->delete();

        $response = $this->post('/login', ['access_code' => 'ZADY-TEST']);
        
        $response->assertSessionHasErrors(['access_code' => 'الكود غير صحيح، تواصل مع الإدارة']);
        $this->assertGuest();
    }

    public function test_rate_limit_blocks_after_10_attempts()
    {
        // First 10 attempts
        for ($i = 0; $i < 10; $i++) {
            $this->post('/login', ['access_code' => 'ZADY-FAIL']);
        }

        // 11th attempt
        $response = $this->post('/login', ['access_code' => 'ZADY-FAIL']);
        
        $response->assertStatus(429); // Too Many Requests
    }
}
