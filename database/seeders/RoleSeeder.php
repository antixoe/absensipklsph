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
        // Clear all existing roles
        Role::query()->delete();

        $roles = [
            [
                'name' => Role::KESISWAAN,
                'description' => 'Admin - Mengelola sistem, membuat QR code, mengelola pengguna',
            ],
            [
                'name' => Role::GURU,
                'description' => 'Guru - Pemindai QR, memvalidasi kehadiran siswa',
            ],
            [
                'name' => Role::MURID,
                'description' => 'Murid - Siswa dengan QR code di kartu pelajar',
            ],
        ];

        foreach ($roles as $roleData) {
            Role::create($roleData);
        }

        echo "✓ Roles created successfully!\n";
        echo "  - Admin (Kesiswaan)\n";
        echo "  - Guru (Teacher)\n";
        echo "  - Murid (Student)\n";
    }
}

