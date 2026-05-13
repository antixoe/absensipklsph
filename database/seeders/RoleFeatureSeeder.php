<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Feature;
use Illuminate\Database\Seeder;

class RoleFeatureSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing features and role features
        \DB::table('role_features')->delete();
        Feature::query()->delete();

        // Define features for the new system
        $features = [
            // Admin/Kesiswaan features
            ['name' => 'Create QR Code', 'slug' => 'create_qrcode', 'description' => 'Ability to create and manage QR codes'],
            ['name' => 'Manage System', 'slug' => 'manage_system', 'description' => 'Ability to manage system settings'],
            ['name' => 'Manage Users', 'slug' => 'manage_users', 'description' => 'Ability to manage users'],
            ['name' => 'Manage Roles', 'slug' => 'manage_roles', 'description' => 'Ability to manage roles and permissions'],
            
            // Teacher/Guru features
            ['name' => 'Scan QR Code', 'slug' => 'scan_qrcode', 'description' => 'Ability to scan student QR codes'],
            ['name' => 'Record Attendance', 'slug' => 'record_attendance', 'description' => 'Ability to record student attendance'],
            ['name' => 'View Attendance', 'slug' => 'view_attendance', 'description' => 'Ability to view attendance records'],
            
            // Student/Murid features
            ['name' => 'View QR Code', 'slug' => 'view_qrcode', 'description' => 'Ability to view personal QR code'],
            ['name' => 'View Attendance Status', 'slug' => 'view_attendance_status', 'description' => 'Ability to view personal attendance status'],
            
            // Wali Kelas features
            ['name' => 'View Class Attendance', 'slug' => 'view_class_attendance', 'description' => 'Ability to view class attendance'],
            
            // Kurikulum features
            ['name' => 'Manage Curriculum', 'slug' => 'manage_curriculum', 'description' => 'Ability to manage curriculum'],
            ['name' => 'View Reports', 'slug' => 'view_reports', 'description' => 'Ability to view reports'],
        ];

        foreach ($features as $featureData) {
            Feature::firstOrCreate(
                ['slug' => $featureData['slug']],
                $featureData
            );
        }

        // Define roles and their associated features
        $roleFeatures = [
            Role::KESISWAAN => [
                'create_qrcode',
                'manage_system',
                'manage_users',
                'manage_roles',
                'view_reports',
            ],
            Role::KURIKULUM => [
                'manage_curriculum',
                'view_reports',
                'view_class_attendance',
            ],
            Role::WALI_KELAS => [
                'view_class_attendance',
                'view_reports',
            ],
            Role::GURU => [
                'scan_qrcode',
                'record_attendance',
                'view_attendance',
                'view_class_attendance',
            ],
            Role::MURID => [
                'view_qrcode',
                'view_attendance_status',
            ],
            Role::KETUA_KELAS => [
                'view_class_attendance',
            ],
            Role::SEKRETARIS_KELAS => [
                'view_class_attendance',
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

            // Sync features
            $role->features()->sync($featureIds);
        }

        echo "✓ Features assigned to roles successfully!\n";
    }
}

