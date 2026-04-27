<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('students') || !Schema::hasTable('users') || !Schema::hasTable('roles')) {
            return;
        }

        $studentRoleId = DB::table('roles')
            ->where('name', 'student')
            ->value('id');

        if (!$studentRoleId) {
            return;
        }

        DB::table('users')
            ->where('role_id', $studentRoleId)
            ->whereNull('deleted_at')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('students')
                    ->whereColumn('students.user_id', 'users.id');
            })
            ->orderBy('id')
            ->chunkById(100, function ($users) {
                $now = now();

                foreach ($users as $user) {
                    DB::table('students')->insert([
                        'user_id' => $user->id,
                        'internship_program_id' => null,
                        'nim' => 'AUTO-' . $user->id,
                        'school' => null,
                        'major' => null,
                        'phone' => $user->phone,
                        'company_placement' => null,
                        'start_date' => null,
                        'end_date' => null,
                        'status' => 'active',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }, 'id');
    }

    public function down(): void
    {
        if (!Schema::hasTable('students')) {
            return;
        }

        DB::table('students')
            ->where('nim', 'like', 'AUTO-%')
            ->whereNull('school')
            ->whereNull('major')
            ->whereNull('company_placement')
            ->whereNull('start_date')
            ->whereNull('end_date')
            ->delete();
    }
};
