<?php

namespace Tests\Feature;

use App\Services\SubscriptionService;
use App\Models\User;
use App\Models\Student;
use App\Models\Group;
use App\Models\Enrollment;
use App\Models\Subscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionServiceTest extends TestCase
{
    use RefreshDatabase;

    protected SubscriptionService $service;

    protected function setUp(): void
    {
        parent::setUp();
        // Since GenerateMonthlySubscriptions runs via CLI or job, we might not have an authenticated user.
        // Auditable trait expects user, so we should authenticate or bypass.
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $this->service = app(SubscriptionService::class);
    }

    public function test_generate_creates_for_active_enrollments()
    {
        $student = Student::factory()->create();
        $group = Group::factory()->create();
        
        Enrollment::factory()->create([
            'student_id' => $student->id,
            'group_id' => $group->id,
            'active' => true
        ]);

        $this->service->generateForMonth('2026-06');

        $this->assertDatabaseHas('subscriptions', [
            'student_id' => $student->id,
            'group_id' => $group->id,
            'month' => '2026-06',
            'status' => 'unpaid'
        ]);
    }

    public function test_generate_skips_inactive_enrollments()
    {
        $student = Student::factory()->create();
        $group = Group::factory()->create();
        
        Enrollment::factory()->create([
            'student_id' => $student->id,
            'group_id' => $group->id,
            'active' => false
        ]);

        $this->service->generateForMonth('2026-06');

        $this->assertDatabaseMissing('subscriptions', [
            'student_id' => $student->id,
            'group_id' => $group->id,
            'month' => '2026-06'
        ]);
    }

    public function test_generate_is_idempotent()
    {
        $student = Student::factory()->create();
        $group = Group::factory()->create();
        
        Enrollment::factory()->create([
            'student_id' => $student->id,
            'group_id' => $group->id,
            'active' => true
        ]);

        $this->service->generateForMonth('2026-06');
        $this->assertDatabaseCount('subscriptions', 1);

        // Run again
        $this->service->generateForMonth('2026-06');
        $this->assertDatabaseCount('subscriptions', 1);
    }
}
