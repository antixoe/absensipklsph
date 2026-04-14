<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Absensi PKL</title>
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

        .login-container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-header h1 {
            font-size: 28px;
            color: #f97316;
            margin-bottom: 10px;
        }

        .login-header p {
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
        input[type="password"] {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e5e7eb;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        .password-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .password-wrapper input[type="password"],
        .password-wrapper input[type="text"] {
            padding-right: 45px;
        }

        .password-toggle {
            position: absolute;
            right: 12px;
            background: none;
            border: none;
            cursor: pointer;
            color: #666;
            font-size: 18px;
            padding: 0;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.3s;
        }

        .password-toggle:hover {
            color: #f97316;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #f97316;
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

        .login-footer {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #666;
        }

        .login-footer a {
            color: #f97316;
            text-decoration: none;
            font-weight: 600;
        }

        .login-footer a:hover {
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
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

    <div class="login-container">
        <div class="login-header">
            <img src="https://www.permataharapanku.sch.id/images/logo_sph.png" alt="School Logo" class="logo-img">
            <h1>Absensi PKL</h1>
            <p>Student Internship Attendance & Logbook</p>
        </div>

        @if ($errors->any())
            <div class="alert">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('auth.login') }}">
            @csrf

            <div class="form-group">
                <label for="email">Email Address</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                >
                @error('email')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="password-wrapper">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                    >
                    <button type="button" class="password-toggle" onclick="togglePassword('password')">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
                @error('password')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit">Log In</button>
        </form>

        <div class="login-footer">
            Don't have an account? <a href="{{ route('register') }}">Register here</a>
        </div>
    </div>

    <script>
        function togglePassword(fieldId) {
            const passwordField = document.getElementById(fieldId);
            const toggleButton = document.querySelector(`button[onclick="togglePassword('${fieldId}')"`);
            const icon = toggleButton.querySelector('i');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                passwordField.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        }
    </script>
</body>
</html>
