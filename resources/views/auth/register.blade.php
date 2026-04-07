<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Absensi PKL</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .register-container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 500px;
            max-height: 90vh;
            overflow-y: auto;
        }

        .register-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .register-header h1 {
            font-size: 28px;
            color: #f97316;
            margin-bottom: 10px;
        }

        .register-header p {
            color: #666;
            font-size: 14px;
        }

        .logo-img {
            height: 80px;
            width: auto;
            object-fit: contain;
            margin-bottom: 15px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
            font-size: 14px;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e5e7eb;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.3s;
            font-family: inherit;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus,
        select:focus {
            outline: none;
            border-color: #f97316;
        }

        .role-selector {
            display: flex;
            gap: 15px;
            margin-bottom: 25px;
        }

        .role-option {
            flex: 1;
            padding: 12px;
            border: 2px solid #e5e7eb;
            border-radius: 6px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }

        .role-option input[type="radio"] {
            display: none;
        }

        .role-option input[type="radio"]:checked + label {
            color: white;
        }

        .role-option input[type="radio"]:checked {
            border-color: #f97316;
        }

        .role-option:has(input[type="radio"]:checked) {
            background: #f97316;
            border-color: #f97316;
            color: white;
        }

        .role-option:has(input[type="radio"]:checked) label {
            color: white;
            cursor: pointer;
        }

        .role-option label {
            margin: 0;
            cursor: pointer;
            font-weight: 600;
        }

        .hidden-section {
            display: none;
        }

        .hidden-section.active {
            display: block;
        }

        .error-message {
            color: #dc2626;
            font-size: 13px;
            margin-top: 5px;
        }

        .alert {
            background: #fee2e2;
            color: #dc2626;
            padding: 12px 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        button {
            width: 100%;
            padding: 12px 20px;
            background: #f97316;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }

        button:hover {
            background: #ea580c;
        }

        .register-footer {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #666;
        }

        .register-footer a {
            color: #f97316;
            text-decoration: none;
            font-weight: 600;
        }

        .register-footer a:hover {
            text-decoration: underline;
        }

        /* Toast/Popup Styles */
        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 16px 24px;
            border-radius: 8px;
            color: white;
            font-weight: 600;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            animation: slideIn 0.3s ease-out, slideOut 0.3s ease-out 2.7s forwards;
            z-index: 1000;
            max-width: 300px;
            word-wrap: break-word;
        }

        .toast.success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }

        .toast.error {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }

        @keyframes slideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(400px);
                opacity: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Toast Messages -->
    @if (session('success'))
        <div class="toast success">
            <i class="bi bi-check-circle-fill" style="margin-right: 8px;"></i>{{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="toast error">
            <i class="bi bi-x-circle-fill" style="margin-right: 8px;"></i>{{ session('error') }}
        </div>
    @endif

    <div class="register-container">
        <div class="register-header">
            <img src="https://www.permataharapanku.sch.id/images/logo_sph.png" alt="School Logo" class="logo-img">
            <h1>Create Account</h1>
            <p>Join Absensi PKL System</p>
        </div>

        @if ($errors->any())
            <div class="alert">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('auth.register') }}" id="registerForm">
            @csrf

            <!-- Role Selection -->
            <label style="display: block; margin-bottom: 15px; font-weight: 600;">Select Role</label>
            <div class="role-selector">
                <div class="role-option">
                    <input type="radio" id="student" name="role" value="student" checked onchange="toggleRole()">
                    <label for="student"><i class="bi bi-mortarboard" style="margin-right: 5px;"></i>Student</label>
                </div>
                <div class="role-option">
                    <input type="radio" id="instructor" name="role" value="instructor" onchange="toggleRole()">
                    <label for="instructor">👨‍🏫 Instructor</label>
                </div>
            </div>

            <!-- Common Fields -->
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required>
                @error('name')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required>
                @error('email')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="phone">Phone (Optional)</label>
                <input type="text" id="phone" name="phone" value="{{ old('phone') }}">
                @error('phone')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <!-- Student Fields -->
            <div id="studentFields" class="hidden-section active">
                <div class="form-group">
                    <label for="nim">Student ID (NIM)</label>
                    <input type="text" id="nim" name="nim" value="{{ old('nim') }}">
                    @error('nim')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="school">School/University</label>
                    <input type="text" id="school" name="school" value="{{ old('school') }}">
                    @error('school')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="major">Major/Program</label>
                    <input type="text" id="major" name="major" value="{{ old('major') }}">
                    @error('major')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Instructor Fields -->
            <div id="instructorFields" class="hidden-section">
                <div class="form-group">
                    <label for="nip">Employee ID (NIP)</label>
                    <input type="text" id="nip" name="nip" value="{{ old('nip') }}">
                    @error('nip')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="department">Department</label>
                    <input type="text" id="department" name="department" value="{{ old('department') }}">
                    @error('department')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Password Fields -->
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
                @error('password')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirm Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required>
            </div>

            <button type="submit">Create Account</button>
        </form>

        <div class="register-footer">
            Already have an account? <a href="{{ route('login') }}">Log in</a>
        </div>
    </div>

    <script>
        function toggleRole() {
            const role = document.querySelector('input[name="role"]:checked').value;
            const studentFields = document.getElementById('studentFields');
            const instructorFields = document.getElementById('instructorFields');

            if (role === 'student') {
                studentFields.classList.add('active');
                instructorFields.classList.remove('active');
            } else {
                studentFields.classList.remove('active');
                instructorFields.classList.add('active');
            }
        }
    </script>
</body>
</html>
