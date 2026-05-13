<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Student;
use App\Models\Role;
use App\Models\QRCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Show the login form
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Handle login submission
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/dashboard')->with('success', 'Login successful! Welcome back.');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email')->with('error', 'Login failed. Please check your credentials.');
    }

    /**
     * Show the register form
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Handle registration submission
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'role' => ['required', 'in:student,instructor'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'unique:users'],
            'password' => ['required', 'min:8', 'confirmed'],
            'nim' => ['required_if:role,student', 'string'],
            'school' => ['required_if:role,student', 'string'],
            'major' => ['required_if:role,student', 'string'],
            'phone' => ['nullable', 'string'],
            'nip' => ['required_if:role,instructor', 'string'],
            'department' => ['required_if:role,instructor', 'string'],
        ]);

        $roleName = $validated['role'] === 'student'
            ? Role::MURID
            : Role::GURU;

        $roleId = Role::resolveByName($roleName)?->id;

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role_id' => $roleId,
            'phone' => $validated['phone'] ?? null,
        ]);

        if ($validated['role'] === 'student') {
            $student = Student::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nim' => $validated['nim'],
                    'school' => $validated['school'],
                    'major' => $validated['major'],
                    'phone' => $validated['phone'] ?? null,
                ]
            );

            // Auto-generate QR code for student (for ID card)
            if (!$student->qrCode) {
                $qrCode = QRCode::createStudentQRCode($student->id);
                $student->update([
                    'qr_code_id' => $qrCode->id,
                    'student_qr_code' => $qrCode->code,
                ]);
            }
        }

        Auth::login($user);
        return redirect('/dashboard')->with('success', 'Registration successful! Welcome to Absensi Sekolah.');
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login')->with('success', 'You have been logged out successfully.');
    }
}
