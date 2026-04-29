<?php

namespace Tests\Feature;

use App\Models\DailyAgenda;
use App\Models\Instructor;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DailyAgendaIndustryScopeTest extends TestCase
{
    use RefreshDatabase;

    public function test_industry_supervisor_sees_only_agendas_for_their_industry(): void
    {
        $industrySupervisorRole = Role::create([
            'name' => 'industry_supervisor',
            'description' => 'Industry Supervisor',
        ]);

        $studentRole = Role::create([
            'name' => 'student',
            'description' => 'Student',
        ]);

        $supervisor = User::create([
            'name' => 'Supervisor',
            'email' => 'supervisor@example.com',
            'password' => Hash::make('password123'),
            'role_id' => $industrySupervisorRole->id,
            'status' => 'active',
        ]);

        Instructor::create([
            'user_id' => $supervisor->id,
            'nip' => 'INS-001',
            'department' => 'PT ABC',
            'phone' => '08123456789',
        ]);

        $studentOneUser = User::create([
            'name' => 'Student One',
            'email' => 'student1@example.com',
            'password' => Hash::make('password123'),
            'role_id' => $studentRole->id,
            'status' => 'active',
        ]);

        $studentTwoUser = User::create([
            'name' => 'Student Two',
            'email' => 'student2@example.com',
            'password' => Hash::make('password123'),
            'role_id' => $studentRole->id,
            'status' => 'active',
        ]);

        $studentOne = $studentOneUser->student()->firstOrFail();
        $studentOne->update([
            'nim' => 'STU001',
            'school' => 'School A',
            'major' => 'IT',
            'company_placement' => 'PT ABC',
            'status' => 'active',
        ]);

        $studentTwo = $studentTwoUser->student()->firstOrFail();
        $studentTwo->update([
            'nim' => 'STU002',
            'school' => 'School B',
            'major' => 'IT',
            'company_placement' => 'PT XYZ',
            'status' => 'active',
        ]);

        DailyAgenda::create([
            'student_id' => $studentOne->id,
            'agenda_date' => now()->toDateString(),
        ]);

        DailyAgenda::create([
            'student_id' => $studentTwo->id,
            'agenda_date' => now()->toDateString(),
        ]);

        $response = $this->actingAs($supervisor)->get(route('daily-agenda.index'));

        $response->assertOk();
        $response->assertSee('Student One');
        $response->assertDontSee('Student Two');
    }

    public function test_industry_supervisor_cannot_open_agendas_from_other_industries(): void
    {
        $industrySupervisorRole = Role::create([
            'name' => 'industry_supervisor',
            'description' => 'Industry Supervisor',
        ]);

        $studentRole = Role::create([
            'name' => 'student',
            'description' => 'Student',
        ]);

        $supervisor = User::create([
            'name' => 'Supervisor',
            'email' => 'supervisor@example.com',
            'password' => Hash::make('password123'),
            'role_id' => $industrySupervisorRole->id,
            'status' => 'active',
        ]);

        Instructor::create([
            'user_id' => $supervisor->id,
            'nip' => 'INS-001',
            'department' => 'PT ABC',
            'phone' => '08123456789',
        ]);

        $studentUser = User::create([
            'name' => 'Student Two',
            'email' => 'student2@example.com',
            'password' => Hash::make('password123'),
            'role_id' => $studentRole->id,
            'status' => 'active',
        ]);

        $student = $studentUser->student()->firstOrFail();
        $student->update([
            'nim' => 'STU002',
            'school' => 'School B',
            'major' => 'IT',
            'company_placement' => 'PT XYZ',
            'status' => 'active',
        ]);

        $agenda = DailyAgenda::create([
            'student_id' => $student->id,
            'agenda_date' => now()->toDateString(),
        ]);

        $response = $this->actingAs($supervisor)->get(route('daily-agenda.show', $agenda->id));

        $response->assertRedirect(route('daily-agenda.index'));
        $response->assertSessionHas('error', 'Industry supervisors can only access agendas from their own industry.');
    }
}
