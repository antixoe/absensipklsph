<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Core roles for RBAC system
        Role::firstOrCreate(
            ['name' => 'admin'],
            ['description' => 'Super Administrator - Full system access']
        );

        Role::firstOrCreate(
            ['name' => 'student'],
            ['description' => 'Student - Can check-in/out and fill logbooks']
        );

        Role::firstOrCreate(
            ['name' => 'industry_supervisor'],
            ['description' => 'Industry Supervisor - Validates attendance and logbooks']
        );

        Role::firstOrCreate(
            ['name' => 'head_of_department'],
            ['description' => 'Head of Department - Reviews weekly logbooks']
        );

        Role::firstOrCreate(
            ['name' => 'homeroom_teacher'],
            ['description' => 'Homeroom Teacher - Views class data']
        );

        Role::firstOrCreate(
            ['name' => 'school_principal'],
            ['description' => 'School Principal - Views all school data']
        );
    }
}
