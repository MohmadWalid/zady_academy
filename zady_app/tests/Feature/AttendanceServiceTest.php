<?php

namespace Tests\Feature;

use App\Services\AttendanceService;
use App\Models\User;
use App\Models\Student;
use App\Models\Group;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceServiceTest extends TestCase
{
    use RefreshDatabase;

    protected AttendanceService $service;
    protected User $teacher;
    protected Group $group;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(AttendanceService::class);
        $this->teacher = User::factory()->create(['role' => 'teacher']);
        $this->group = Group::factory()->create(['teacher_id' => $this->teacher->id]);
        $this->actingAs($this->teacher);
    }

    public function test_bulk_record_creates_attendance()
    {
        $student1 = Student::factory()->create();
        $student2 = Student::factory()->create();

        $data = [
            'group_id' => $this->group->id,
            'date' => '2026-05-01',
            'records' => [
                ['student_id' => $student1->id, 'present' => true],
                ['student_id' => $student2->id, 'present' => false],
            ]
        ];

        $this->service->recordBulk(
            $this->group->id,
            '2026-05-01',
            [
                ['student_id' => $student1->id, 'present' => true],
                ['student_id' => $student2->id, 'present' => false],
            ],
            $this->teacher->id
        );

        $this->assertDatabaseHas('attendance', [
            'student_id' => $student1->id,
            'group_id' => $this->group->id,
            'date' => '2026-05-01 00:00:00',
            'present' => true
        ]);

        $this->assertDatabaseHas('attendance', [
            'student_id' => $student2->id,
            'present' => false
        ]);
    }

    public function test_bulk_record_sets_taken_by()
    {
        $student = Student::factory()->create();

        $data = [
            'group_id' => $this->group->id,
            'date' => '2026-05-02',
            'records' => [
                ['student_id' => $student->id, 'present' => true],
            ]
        ];

        $this->service->recordBulk(
            $this->group->id,
            '2026-05-02',
            [
                ['student_id' => $student->id, 'present' => true],
            ],
            $this->teacher->id
        );

        $this->assertDatabaseHas('attendance', [
            'student_id' => $student->id,
            'taken_by' => $this->teacher->id
        ]);
    }

    public function test_bulk_record_is_idempotent()
    {
        $student = Student::factory()->create();

        $data1 = [
            'group_id' => $this->group->id,
            'date' => '2026-05-03',
            'records' => [
                ['student_id' => $student->id, 'present' => true],
            ]
        ];

        $this->service->recordBulk(
            $this->group->id,
            '2026-05-03',
            [
                ['student_id' => $student->id, 'present' => true],
            ],
            $this->teacher->id
        );
        $this->assertDatabaseCount('attendance', 1);

        $data2 = [
            'group_id' => $this->group->id,
            'date' => '2026-05-03',
            'records' => [
                ['student_id' => $student->id, 'present' => false],
            ]
        ];

        $this->service->recordBulk(
            $this->group->id,
            '2026-05-03',
            [
                ['student_id' => $student->id, 'present' => false],
            ],
            $this->teacher->id
        );
        
        $this->assertDatabaseCount('attendance', 1);
        $this->assertDatabaseHas('attendance', [
            'student_id' => $student->id,
            'present' => false
        ]);
    }
}
