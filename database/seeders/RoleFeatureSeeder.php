<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Feature;
use Illuminate\Database\Seeder;

class RoleFeatureSeeder extends Seeder
{
    public function run(): void
    {
        // Define features
        $features = [
            ['name' => 'Check-in/Check-out', 'slug' => 'checkin_checkout', 'description' => 'Ability to check-in and check-out attendance'],
            ['name' => 'Fill Daily Logbook', 'slug' => 'fill_logbook', 'description' => 'Ability to fill daily logbook entries'],
            ['name' => 'View Guidance Notes', 'slug' => 'view_guidance', 'description' => 'Ability to view guidance notes'],
            ['name' => 'Validate Attendance', 'slug' => 'validate_attendance', 'description' => 'Ability to validate attendance records'],
            ['name' => 'Validate Logbook', 'slug' => 'validate_logbook', 'description' => 'Ability to validate logbook entries'],
            ['name' => 'Provide Guidance', 'slug' => 'provide_guidance', 'description' => 'Ability to provide guidance notes'],
            ['name' => 'Weekly Logbook Review', 'slug' => 'weekly_review', 'description' => 'Ability to review logbooks weekly'],
            ['name' => 'Department Filtering', 'slug' => 'department_filter', 'description' => 'Ability to filter by department'],
            ['name' => 'Class Filtering', 'slug' => 'class_filter', 'description' => 'Ability to filter by class'],
            ['name' => 'View All Data', 'slug' => 'view_all_data', 'description' => 'Ability to view all school data'],
            ['name' => 'Manage Roles', 'slug' => 'manage_roles', 'description' => 'Ability to manage roles and permissions'],
            ['name' => 'Manage Users', 'slug' => 'manage_users', 'description' => 'Ability to manage users'],
            ['name' => 'View Reports', 'slug' => 'view_reports', 'description' => 'Ability to view reports'],
            ['name' => 'Manage Activities', 'slug' => 'manage_activities', 'description' => 'Ability to manage activities'],
        ];

        foreach ($features as $featureData) {
            Feature::firstOrCreate(
                ['slug' => $featureData['slug']],
                $featureData
            );
        }

        // Define roles and their associated features
        $roleFeatures = [
            Role::STUDENT => [
                'checkin_checkout',
                'fill_logbook',
                'view_guidance',
            ],
            Role::INDUSTRY_SUPERVISOR => [
                'validate_attendance',
                'validate_logbook',
                'provide_guidance',
                'view_reports',
            ],
            Role::HEAD_OF_DEPARTMENT => [
                'weekly_review',
                'department_filter',
                'view_reports',
            ],
            Role::HOMEROOM_TEACHER => [
                'class_filter',
                'view_reports',
            ],
            Role::SCHOOL_PRINCIPAL => [
                'view_all_data',
                'view_reports',
            ],
            Role::ADMIN => [
                'manage_roles',
                'manage_users',
                'view_all_data',
                'department_filter',
                'class_filter',
                'view_reports',
            ],
        ];

        // Create roles and assign features
        foreach ($roleFeatures as $roleName => $featureSlugs) {
            $role = Role::firstOrCreate(
                ['name' => $roleName],
                ['description' => ucfirst(str_replace('_', ' ', $roleName))]
            );

            // Get feature IDs for the slugs
            $featureIds = Feature::whereIn('slug', $featureSlugs)->pluck('id')->toArray();

            // Sync features (this will attach new ones and detach old ones)
            $role->features()->sync($featureIds);
        }
    }
}
