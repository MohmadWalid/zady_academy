<?php

namespace Tests\Feature;

use App\Services\StudentService;
use App\Models\User;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentServiceTest extends TestCase
{
    use RefreshDatabase;

    protected StudentService $service;

    protected function setUp(): void
    {
        parent::setUp();
        // create an admin to satisfy created_by
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $this->service = app(StudentService::class);
    }

    public function test_creates_new_parent_when_phone_not_found()
    {
        $data = [
            'name' => 'Test Student',
            'parent_name' => 'Test Parent',
            'phone' => '0500000001',
            'age' => 10
        ];

        $student = $this->service->createWithParent($data, '0500000001', 'Test Parent')['student'];

        $this->assertInstanceOf(Student::class, $student);
        $this->assertDatabaseHas('users', [
            'name' => 'Test Parent',
            'phone' => '0500000001',
            'role' => 'parent'
        ]);
        $this->assertDatabaseHas('students', [
            'name' => 'Test Student'
        ]);
    }

    public function test_reuses_existing_parent()
    {
        $parent = User::factory()->create([
            'role' => 'parent',
            'phone' => '0500000002'
        ]);

        $data = [
            'name' => 'Student Two',
            'parent_name' => 'Different Parent Name', // Should ignore
            'phone' => '0500000002',
            'age' => 12
        ];

        $student = $this->service->createWithParent($data, '0500000002')['student'];

        $this->assertEquals($parent->id, $student->parent_id);
        $this->assertDatabaseCount('users', 2); // 1 admin + 1 parent
    }

    public function test_rejects_phone_with_wrong_role()
    {
        User::factory()->create([
            'role' => 'teacher',
            'phone' => '0500000003'
        ]);

        $data = [
            'name' => 'Student Three',
            'parent_name' => 'Test',
            'phone' => '0500000003',
            'age' => 15
        ];

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('هذا الرقم مسجل بدور مختلف');

        $this->service->createWithParent($data, '0500000003');
    }
}
