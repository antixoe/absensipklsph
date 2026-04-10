<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use PhpOffice\PhpSpreadsheet\IOFactory;

class UsersImport
{
    /**
     * Import users from Excel/CSV file
     */
    public static function fromFile($filePath)
    {
        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();
        
        if (empty($rows)) {
            throw new \Exception("File is empty");
        }

        // Get headers from first row
        $headers = array_map('strtolower', $rows[0]);
        $createdCount = 0;
        $skippedCount = 0;
        $errors = [];

        // Process each row (skip header)
        foreach (array_slice($rows, 1) as $rowIndex => $row) {
            try {
                // Map row values to headers
                $rowData = array_combine($headers, $row);
                
                // Handle flexible column names
                $fullName = $rowData['name'] ?? '';
                if (!$fullName && isset($rowData['firstname']) && isset($rowData['lastname'])) {
                    $fullName = trim($rowData['firstname'] . ' ' . $rowData['lastname']);
                } elseif (!$fullName && isset($rowData['firstname'])) {
                    $fullName = $rowData['firstname'];
                } elseif (!$fullName && isset($rowData['username'])) {
                    $fullName = $rowData['username'];
                }

                $email = trim($rowData['email'] ?? '');
                $password = $rowData['password'] ?? 'password';
                $role_name = $rowData['role'] ?? 'student';

                // Validate required fields
                if (!$email || !$fullName) {
                    $errors[] = "Row " . ($rowIndex + 2) . ": Email and name are required";
                    $skippedCount++;
                    continue;
                }

                // Validate email
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $errors[] = "Row " . ($rowIndex + 2) . ": Invalid email format";
                    $skippedCount++;
                    continue;
                }

                // Check if user already exists
                $existing = User::where('email', $email)->first();
                if ($existing) {
                    $skippedCount++;
                    continue;
                }

                // Get or create role
                $role = Role::where('name', $role_name)->first();
                if (!$role) {
                    $role = Role::where('slug', $role_name)->first();
                }
                if (!$role) {
                    $role = Role::first(); // Use first role as default
                }

                // Create user
                User::create([
                    'name' => $fullName,
                    'email' => $email,
                    'password' => Hash::make($password),
                    'phone' => $rowData['phone'] ?? null,
                    'address' => $rowData['address'] ?? null,
                    'role_id' => $role->id,
                    'status' => $rowData['status'] ?? 'active',
                ]);

                $createdCount++;
            } catch (\Exception $e) {
                $errors[] = "Row " . ($rowIndex + 2) . ": " . $e->getMessage();
                $skippedCount++;
            }
        }

        return [
            'created' => $createdCount,
            'skipped' => $skippedCount,
            'errors' => $errors,
        ];
    }
}
