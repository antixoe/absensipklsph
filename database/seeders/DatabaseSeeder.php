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

        // Create admin user if it doesn't exist
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('admin123'),
                'status' => 'active',
                'role_id' => 1, // Admin role
            ]
        );

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

        // Create additional test students for absence data
        $additionalStudents = ['student2@example.com', 'student3@example.com', 
                              'student4@example.com', 'student5@example.com',
                              'student6@example.com', 'student7@example.com',
                              'student8@example.com', 'student9@example.com',
                              'student10@example.com', 'student11@example.com',
                              'student12@example.com', 'student13@example.com',
                              'student14@example.com', 'student15@example.com'];

        foreach ($additionalStudents as $index => $email) {
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => 'Student ' . ($index + 2),
                    'password' => Hash::make('password123'),
                    'status' => 'active',
                    'role_id' => 2, // Student role
                ]
            );

            Student::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'internship_program_id' => $program->id,
                    'nim' => 'STU' . str_pad($index + 2, 3, '0', STR_PAD_LEFT),
                    'school' => 'Test University',
                    'major' => 'Information Technology',
                    'phone' => '0812345678' . str_pad($index, 2, '0', STR_PAD_LEFT),
                ]
            );
        }
    }
}
