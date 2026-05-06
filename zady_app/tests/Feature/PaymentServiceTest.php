<?php

namespace Tests\Feature;

use App\Services\PaymentService;
use App\Models\User;
use App\Models\Student;
use App\Models\Group;
use App\Models\Subscription;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentServiceTest extends TestCase
{
    use RefreshDatabase;

    protected PaymentService $service;
    protected User $admin;
    protected User $parent;
    protected Subscription $subscription;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(PaymentService::class);
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->parent = User::factory()->create(['role' => 'parent']);
        
        $student = Student::factory()->create(['parent_id' => $this->parent->id]);
        $group = Group::factory()->create();
        
        $this->subscription = Subscription::factory()->create([
            'student_id' => $student->id,
            'group_id' => $group->id,
            'month' => '2026-05',
            'status' => 'unpaid'
        ]);

        $this->actingAs($this->admin);
    }

    public function test_cash_payment_creates_approved_and_sets_subscription_paid()
    {
        $payment = $this->service->recordCash(
            $this->subscription,
            500
        );

        $this->assertEquals('approved', $payment->status);
        $this->assertEquals('cash', $payment->method);
        
        $this->subscription->refresh();
        $this->assertEquals('paid', $this->subscription->status);
    }

    public function test_cash_payment_rejected_if_already_paid()
    {
        $this->subscription->update(['status' => 'paid']);

        $this->expectException(\Exception::class);
        
        $this->service->recordCash(
            $this->subscription,
            500
        );
    }

    public function test_transfer_sets_pending()
    {
        $this->actingAs($this->parent);

        $payment = $this->service->submitTransfer(
            $this->subscription,
            'fake_path.jpg',
            500
        );

        $this->assertEquals('pending', $payment->status);
        $this->assertEquals('transfer', $payment->method);

        $this->subscription->refresh();
        $this->assertEquals('pending', $this->subscription->status);
    }

    public function test_approve_sets_paid()
    {
        $payment = Payment::factory()->create([
            'subscription_id' => $this->subscription->id,
            'status' => 'pending',
            'method' => 'transfer'
        ]);
        $this->subscription->update(['status' => 'pending']);

        $this->service->approve($payment);

        $payment->refresh();
        $this->assertEquals('approved', $payment->status);

        $this->subscription->refresh();
        $this->assertEquals('paid', $this->subscription->status);
    }

    public function test_reject_sets_unpaid()
    {
        $payment = Payment::factory()->create([
            'subscription_id' => $this->subscription->id,
            'status' => 'pending',
            'method' => 'transfer'
        ]);
        $this->subscription->update(['status' => 'pending']);

        $this->service->reject($payment);

        $payment->refresh();
        $this->assertEquals('rejected', $payment->status);

        $this->subscription->refresh();
        $this->assertEquals('unpaid', $this->subscription->status);
    }

    public function test_refund_sets_refunded_and_subscription_unpaid()
    {
        $payment = Payment::factory()->create([
            'subscription_id' => $this->subscription->id,
            'status' => 'approved',
            'method' => 'cash'
        ]);
        $this->subscription->update(['status' => 'paid']);

        $this->service->refund($payment);

        $payment->refresh();
        $this->assertEquals('refunded', $payment->status);

        $this->subscription->refresh();
        $this->assertEquals('unpaid', $this->subscription->status);
    }

    public function test_refund_admin_only()
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        
        $payment = Payment::factory()->create([
            'subscription_id' => $this->subscription->id,
            'status' => 'approved',
            'method' => 'cash'
        ]);

        $this->assertTrue($this->admin->can('refund', $payment));
        $this->assertFalse($teacher->can('refund', $payment));
    }
}
