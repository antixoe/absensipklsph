<?php

namespace Database\Seeders;

use App\Models\Absence;
use App\Models\Student;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class AbsenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing students
        $students = Student::with('user')->get();

        if ($students->isEmpty()) {
            echo "No students found. Please run database seeders first.\n";
            return;
        }

        // Get or create admin user for approvals
        $adminUser = \App\Models\User::where('email', 'admin@example.com')->first();
        if (!$adminUser) {
            echo "Admin user not found. Skipping approved/rejected records.\n";
            $approverUserId = null;
        } else {
            $approverUserId = $adminUser->id;
        }

        // Sample locations and IP addresses
        $locations = [
            ['name' => 'Jakarta, Indonesia', 'lat' => -6.2088, 'lng' => 106.8456],
            ['name' => 'Bandung, Indonesia', 'lat' => -6.9175, 'lng' => 107.6191],
            ['name' => 'Surabaya, Indonesia', 'lat' => -7.2506, 'lng' => 112.7508],
            ['name' => 'Medan, Indonesia', 'lat' => 2.1949, 'lng' => 99.1198],
            ['name' => 'Yogyakarta, Indonesia', 'lat' => -7.7956, 'lng' => 110.3695],
        ];

        $ips = [
            '192.168.1.1',
            '192.168.1.2',
            '10.0.0.1',
            '172.16.0.1',
            '203.192.1.1',
            '119.81.1.1',
            '125.160.1.1',
        ];

        $notes = [
            'Sakit',
            'Ada keperluan keluarga',
            'Pulang kampung',
            'Mengurus dokumen',
            'Tidak ada kabar',
            'Sakit mata',
            'Sakit gigi',
        ];

        // Create 8 pending absence records for testing
        $absenceDates = [
            Carbon::now()->subDays(2),
            Carbon::now()->subDays(2),
            Carbon::now()->subDays(1),
            Carbon::now()->subDays(1),
            Carbon::now(),
            Carbon::now(),
            Carbon::now(),
            Carbon::now()->addDays(1),
        ];

        foreach (array_slice($students->toArray(), 0, 8) as $index => $student) {
            $location = $locations[$index % count($locations)];
            
            Absence::create([
                'student_id' => $student['id'],
                'absence_date' => $absenceDates[$index],
                'selfie_path' => 'absences/dummy_selfie_' . ($index + 1) . '.jpg',
                'ip_address' => $ips[$index % count($ips)],
                'latitude' => $location['lat'] + (rand(-10, 10) / 1000),
                'longitude' => $location['lng'] + (rand(-10, 10) / 1000),
                'location_name' => $location['name'],
                'notes' => $notes[$index % count($notes)],
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Create 3 approved absence records (only if admin user exists)
        if ($approverUserId) {
            foreach (array_slice($students->toArray(), 8, 3) as $index => $student) {
                $location = $locations[$index % count($locations)];
                
                Absence::create([
                    'student_id' => $student['id'],
                    'absence_date' => Carbon::now()->subDays(5),
                    'selfie_path' => 'absences/approved_selfie_' . ($index + 1) . '.jpg',
                    'ip_address' => $ips[$index % count($ips)],
                    'latitude' => $location['lat'] + (rand(-10, 10) / 1000),
                    'longitude' => $location['lng'] + (rand(-10, 10) / 1000),
                    'location_name' => $location['name'],
                    'notes' => 'Sakit - dengan bukti medical check-up',
                    'status' => 'approved',
                    'approved_signature' => 'signatures/dummy_signature_' . ($index + 1) . '.png',
                    'approved_notes' => 'Disetujui berdasarkan bukti medis',
                    'approved_at' => Carbon::now()->subDays(4),
                    'approved_by' => $approverUserId,
                    'created_at' => Carbon::now()->subDays(5),
                    'updated_at' => Carbon::now()->subDays(4),
                ]);
            }

            // Create 2 rejected absence records
            foreach (array_slice($students->toArray(), 11, 2) as $index => $student) {
                $location = $locations[$index % count($locations)];
                
                Absence::create([
                    'student_id' => $student['id'],
                    'absence_date' => Carbon::now()->subDays(7),
                    'selfie_path' => 'absences/rejected_selfie_' . ($index + 1) . '.jpg',
                    'ip_address' => $ips[$index % count($ips)],
                    'latitude' => $location['lat'] + (rand(-10, 10) / 1000),
                    'longitude' => $location['lng'] + (rand(-10, 10) / 1000),
                    'location_name' => $location['name'],
                    'notes' => 'Tidak ada alasan',
                    'status' => 'rejected',
                    'approved_signature' => 'signatures/dummy_signature_rejected_' . ($index + 1) . '.png',
                    'approved_notes' => 'Tidak dapat disetujui - tidak ada bukti medical check-up',
                    'approved_at' => Carbon::now()->subDays(6),
                    'approved_by' => $approverUserId,
                    'created_at' => Carbon::now()->subDays(7),
                    'updated_at' => Carbon::now()->subDays(6),
                ]);
            }
        }

        echo "✓ Absence dummy data created successfully!\n";
        echo "  - 8 PENDING absences (ready for approval)\n";
        if ($approverUserId) {
            echo "  - 3 APPROVED absences\n";
            echo "  - 2 REJECTED absences\n";
        } else {
            echo "  - Approved/Rejected records skipped (admin user not found)\n";
        }
    }
}

