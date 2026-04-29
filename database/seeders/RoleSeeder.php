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
        $roles = [
            [
                'name' => Role::ADMIN,
                'description' => 'Administrator - akses penuh ke sistem',
                'aliases' => ['admin', 'administrator', 'administrator_sistem'],
            ],
            [
                'name' => Role::STUDENT,
                'description' => 'Siswa - absen, logbook, dan agenda harian',
                'aliases' => ['student', 'siswa', 'murid'],
            ],
            [
                'name' => Role::INDUSTRY_SUPERVISOR,
                'description' => 'Pembimbing PKL - validasi absensi dan logbook siswa',
                'aliases' => ['industry_supervisor', 'pembimbing_pkl', 'pembimbing_perusahaan', 'pembimbing_industri'],
            ],
            [
                'name' => Role::SCHOOL_SUPERVISOR,
                'description' => 'Pembimbing Sekolah - memantau dan membimbing siswa',
                'aliases' => ['pembimbing_sekolah', 'guru_pembimbing', 'guru_pembimbing_sekolah'],
            ],
            [
                'name' => Role::HEAD_OF_DEPARTMENT,
                'description' => 'Kepala Jurusan - memantau laporan dan review berkala',
                'aliases' => ['head_of_department', 'kepala_jurusan', 'ketua_jurusan'],
            ],
            [
                'name' => Role::HOMEROOM_TEACHER,
                'description' => 'Wali Kelas - melihat data dan laporan kelas',
                'aliases' => ['homeroom_teacher', 'wali_kelas', 'walikelas'],
            ],
            [
                'name' => Role::SCHOOL_PRINCIPAL,
                'description' => 'Kepala Sekolah - melihat seluruh data sekolah',
                'aliases' => ['school_principal', 'kepala_sekolah'],
            ],
            [
                'name' => Role::STUDENT_AFFAIRS,
                'description' => 'Kesiswaan - memantau data dan laporan siswa',
                'aliases' => ['kesiswaan', 'student_affairs'],
            ],
        ];

        foreach ($roles as $roleData) {
            $this->upsertRole($roleData['name'], $roleData['description'], $roleData['aliases']);
        }
    }

    /**
     * Create or normalize a role record by canonical name.
     */
    private function upsertRole(string $canonicalName, string $description, array $aliases): void
    {
        $role = Role::resolveByName($canonicalName);

        if (!$role) {
            foreach ($aliases as $alias) {
                $role = Role::resolveByName($alias);
                if ($role) {
                    break;
                }
            }
        }

        if ($role) {
            $role->update([
                'name' => $canonicalName,
                'description' => $description,
            ]);
            return;
        }

        Role::create([
            'name' => $canonicalName,
            'description' => $description,
        ]);
    }
}
