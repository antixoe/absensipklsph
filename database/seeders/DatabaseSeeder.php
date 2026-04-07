<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Student;
use App\Models\Instructor;
use App\Models\InternshipProgram;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed roles and features
        $this->call(RoleSeeder::class);
        $this->call(RoleFeatureSeeder::class);

        // Create test internship program
        $program = InternshipProgram::firstOrCreate(
            ['name' => 'Test Program'],
            ['description' => 'Test Internship Program', 'start_date' => now(), 'end_date' => now()->addMonths(3)]
        );

        // Create test student user if it doesn't exist
        $studentUser = User::firstOrCreate(
            ['email' => 'student@example.com'],
            [
                'name' => 'John Student',
                'password' => Hash::make('password123'),
                'role_id' => 2, // Student role
            ]
        );

        Student::firstOrCreate(
            ['user_id' => $studentUser->id],
            [
                'internship_program_id' => $program->id,
                'nim' => 'STU001',
                'school' => 'Test University',
                'major' => 'Information Technology',
                'phone' => '08123456789',
            ]
        );

        // Create test instructor user if it doesn't exist
        $instructorUser = User::firstOrCreate(
            ['email' => 'instructor@example.com'],
            [
                'name' => 'Jane Instructor',
                'password' => Hash::make('password123'),
                'role_id' => 3, // Instructor role
            ]
        );

        Instructor::firstOrCreate(
            ['user_id' => $instructorUser->id],
            [
                'nip' => 'INS001',
                'department' => 'IT Department',
                'phone' => '08987654321',
            ]
        );
    }
}
