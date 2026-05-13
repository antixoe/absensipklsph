<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('user_qr_code_id')
                ->nullable()
                ->unique()
                ->after('role_id')
                ->constrained('q_r_codes')
                ->nullOnDelete();
        });

        $users = DB::table('users')
            ->whereNull('user_qr_code_id')
            ->orderBy('id')
            ->get();

        foreach ($users as $user) {
            do {
                $code = 'QR-' . strtoupper(bin2hex(random_bytes(8)));
            } while (DB::table('q_r_codes')->where('code', $code)->exists());

            $qrCodeId = DB::table('q_r_codes')->insertGetId([
                'code' => $code,
                'qr_date' => now(),
                'created_by' => $user->id,
                'status' => 'active',
                'notes' => 'Personal QR code for user #' . $user->id,
                'expires_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('users')
                ->where('id', $user->id)
                ->update(['user_qr_code_id' => $qrCodeId]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_qr_code_id');
        });
    }
};
