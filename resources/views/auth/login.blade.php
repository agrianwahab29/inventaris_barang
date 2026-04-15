<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Inventaris</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('image/tut-wuri-handayani.png') }}?v=4">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v=4">
    <link rel="icon" type="image/svg+xml" href="{{ asset('image/tut-wuri-handayani.svg') }}?v=4">

    <style>
        /* Modern Government Design System - Balai Bahasa Sultra */
        :root {
            /* Primary Colors - Elegant Blue Theme */
            --primary-blue: #1a5fb4;
            --primary-blue-dark: #13448a;
            --primary-blue-light: #3584e4;
            --accent-gold: #c5a065;
            --accent-gold-light: #d4b87a;
            
            /* Background Colors */
            --bg-gradient-start: #f0f4f8;
            --bg-gradient-end: #e8eef5;
            --card-bg: #ffffff;
            --card-border: #e1e8ed;
            
            /* Text Colors */
            --text-primary: #1a1a2e;
            --text-secondary: #4a5568;
            --text-muted: #718096;
            --text-white: #ffffff;
            
            /* Status Colors */
            --success-green: #2d6a4f;
            --error-red: #c53030;
            --warning-orange: #c05621;
            
            /* Typography */
            --font-primary: 'Segoe UI', 'Roboto', 'Helvetica Neue', Arial, sans-serif;
            --font-heading: 'Georgia', 'Times New Roman', serif;
        }

        /* Reset & Base */
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html {
            font-size: 16px;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        body {
            font-family: var(--font-primary);
            background: linear-gradient(135deg, var(--bg-gradient-start) 0%, var(--bg-gradient-end) 100%);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 24px;
            line-height: 1.6;
        }

        /* Decorative Background Pattern */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                radial-gradient(circle at 10% 20%, rgba(26, 95, 180, 0.03) 0%, transparent 20%),
                radial-gradient(circle at 90% 80%, rgba(197, 160, 101, 0.03) 0%, transparent 20%);
            pointer-events: none;
            z-index: -1;
        }

        /* Main Container */
        .login-container {
            width: 100%;
            max-width: 480px;
            animation: fadeInUp 0.6s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Header Section */
        .header-section {
            text-align: center;
            margin-bottom: 32px;
        }

        /* Logo Container */
        .logo-container {
            display: flex;
            justify-content: center;
            gap: 24px;
            margin-bottom: 24px;
        }

        .logo {
            width: 80px;
            height: 80px;
            object-fit: contain;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));
        }

        /* Institution Name */
        .institution-name {
            font-family: var(--font-heading);
            font-size: 18px;
            font-weight: 600;
            color: var(--primary-blue);
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .institution-subtitle {
            font-size: 14px;
            color: var(--text-muted);
            margin-bottom: 4px;
        }

        .system-title {
            font-size: 24px;
            font-weight: 700;
            color: var(--text-primary);
            margin-top: 16px;
            position: relative;
            display: inline-block;
        }

        .system-title::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 3px;
            background: linear-gradient(90deg, var(--primary-blue) 0%, var(--accent-gold) 100%);
            border-radius: 2px;
        }

        /* Login Card */
        .login-card {
            background-color: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 12px;
            padding: 40px;
            box-shadow: 
                0 4px 6px -1px rgba(0, 0, 0, 0.05),
                0 10px 15px -3px rgba(0, 0, 0, 0.08),
                0 0 0 1px rgba(26, 95, 180, 0.05);
        }

        /* Error Message */
        .error-message {
            background-color: rgba(197, 48, 48, 0.08);
            border-left: 4px solid var(--error-red);
            border-radius: 6px;
            padding: 12px 16px;
            margin-bottom: 24px;
            font-size: 14px;
            color: var(--error-red);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .error-icon {
            font-size: 18px;
            flex-shrink: 0;
        }

        /* Form Elements */
        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 8px;
        }

        .form-input {
            width: 100%;
            background-color: #fafbfc;
            border: 2px solid #e1e8ed;
            border-radius: 8px;
            padding: 14px 16px;
            font-family: var(--font-primary);
            font-size: 15px;
            font-weight: 400;
            color: var(--text-primary);
            transition: all 0.2s ease;
            -webkit-appearance: none;
            appearance: none;
        }

        .form-input:hover {
            border-color: #d1d9e0;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary-blue);
            background-color: #ffffff;
            box-shadow: 0 0 0 3px rgba(26, 95, 180, 0.1);
        }

        .form-input::placeholder {
            color: #a0aec0;
        }

        /* Password visibility toggle */
        .password-wrapper {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            font-family: var(--font-primary);
            font-size: 13px;
            font-weight: 500;
            padding: 6px 12px;
            border-radius: 6px;
            transition: all 0.2s ease;
        }

        .password-toggle:hover {
            background-color: rgba(26, 95, 180, 0.1);
            color: var(--primary-blue);
        }

        /* Form Options */
        .form-options {
            display: flex;
            justify-content: flex-start;
            align-items: center;
            margin-bottom: 24px;
            font-size: 14px;
        }

        .checkbox-wrapper {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
        }

        .checkbox-input {
            width: 20px;
            height: 20px;
            border: 2px solid #d1d9e0;
            border-radius: 5px;
            background-color: #ffffff;
            appearance: none;
            cursor: pointer;
            position: relative;
            transition: all 0.2s ease;
        }

        .checkbox-input:checked {
            background-color: var(--primary-blue);
            border-color: var(--primary-blue);
        }

        .checkbox-input:checked::after {
            content: '✓';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: #ffffff;
            font-size: 12px;
            font-weight: 700;
        }

        .checkbox-label {
            color: var(--text-secondary);
            font-weight: 500;
            user-select: none;
        }

        /* Submit Button */
        .btn-submit {
            width: 100%;
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-blue-dark) 100%);
            border: none;
            border-radius: 8px;
            padding: 14px 24px;
            font-family: var(--font-primary);
            font-size: 16px;
            font-weight: 600;
            color: var(--text-white);
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(26, 95, 180, 0.3);
            position: relative;
            overflow: hidden;
        }

        .btn-submit::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(
                90deg,
                transparent,
                rgba(255, 255, 255, 0.2),
                transparent
            );
            transition: left 0.5s ease;
        }

        .btn-submit:hover::before {
            left: 100%;
        }

        .btn-submit:hover {
            background: linear-gradient(135deg, var(--primary-blue-dark) 0%, var(--primary-blue) 100%);
            box-shadow: 0 6px 12px rgba(26, 95, 180, 0.4);
            transform: translateY(-1px);
        }

        .btn-submit:active {
            transform: translateY(0);
            box-shadow: 0 2px 4px rgba(26, 95, 180, 0.3);
        }

        .btn-submit:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(26, 95, 180, 0.3);
        }

        /* Footer */
        .login-footer {
            margin-top: 32px;
            text-align: center;
            font-size: 13px;
            color: var(--text-muted);
        }

        .footer-divider {
            color: var(--accent-gold);
            margin: 0 8px;
        }

        /* Security Badge */
        .security-badge {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 16px;
            padding: 8px 16px;
            background-color: rgba(45, 106, 79, 0.08);
            border-radius: 20px;
            font-size: 12px;
            color: var(--success-green);
        }

        .security-icon {
            font-size: 14px;
        }

        /* Responsive Adjustments */
        @media (max-width: 480px) {
            body {
                padding: 16px;
                justify-content: flex-start;
                padding-top: 5vh;
            }

            .login-container {
                max-width: 100%;
            }

            .logo {
                width: 60px;
                height: 60px;
            }

            .institution-name {
                font-size: 16px;
            }

            .system-title {
                font-size: 20px;
            }

            .login-card {
                padding: 28px 20px;
                border-radius: 10px;
            }

            .form-group {
                margin-bottom: 16px;
            }

            .form-input {
                padding: 12px 14px;
                font-size: 16px; /* Prevent iOS zoom */
            }

            .btn-submit {
                padding: 12px 20px;
            }
        }

        @media (max-width: 360px) {
            .logo {
                width: 50px;
                height: 50px;
            }

            .institution-name {
                font-size: 14px;
            }

            .system-title {
                font-size: 18px;
            }

            .login-card {
                padding: 24px 16px;
            }

            .form-label {
                font-size: 13px;
            }
        }

        /* Selection color */
        ::selection {
            background-color: var(--primary-blue);
            color: var(--text-white);
        }
    </style>
</head>

<body>
    <div class="login-container">
        <!-- Header Section -->
        <div class="header-section">
            <!-- Logo Container -->
            <div class="logo-container">
                <img src="{{ asset('image/logo-balai-bahasa.png') }}" alt="Logo Balai Bahasa" class="logo">
                <img src="{{ asset('image/tut-wuri-handayani.png') }}" alt="Logo Kemdikbud" class="logo">
            </div>
            
            <!-- Institution Name -->
            <div class="institution-name">Balai Bahasa Provinsi Sulawesi Tenggara</div>
            <div class="institution-subtitle">Kementerian Pendidikan, Kebudayaan, Riset, dan Teknologi</div>
            
            <!-- System Title -->
            <h1 class="system-title">Sistem Inventaris Barang</h1>
        </div>

        <!-- Login Card -->
        <div class="login-card">
            <!-- Error Messages -->
            @if($errors->any())
            <div class="error-message" role="alert">
                <span class="error-icon">⚠</span>
                <span>{{ $errors->first() }}</span>
            </div>
            @endif

            <!-- Login Form -->
            <form method="POST" action="{{ route('login') }}" novalidate>
                @csrf

                <!-- Username Field -->
                <div class="form-group">
                    <label for="username" class="form-label">Username</label>
                    <input 
                        type="text" 
                        id="username"
                        name="username" 
                        class="form-input" 
                        placeholder="Masukkan username Anda" 
                        required
                        autofocus
                        autocomplete="username"
                        value="{{ old('username') }}"
                    >
                </div>

                <!-- Password Field -->
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <div class="password-wrapper">
                        <input 
                            type="password" 
                            id="password"
                            name="password" 
                            class="form-input" 
                            placeholder="Masukkan password Anda"
                            required
                            autocomplete="current-password"
                        >
                        <button type="button" class="password-toggle" onclick="togglePassword()" aria-label="Toggle password visibility">
                            Tampilkan
                        </button>
                    </div>
                </div>

                <!-- Form Options -->
                <div class="form-options">
                    <label class="checkbox-wrapper">
                        <input type="checkbox" name="remember" class="checkbox-input" id="remember">
                        <span class="checkbox-label">Ingat saya</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn-submit">
                    Masuk ke Sistem
                </button>
            </form>
        </div>

        <!-- Footer -->
        <div class="login-footer">
            <span>© 2026</span>
            <span class="footer-divider">|</span>
            <span>Balai Bahasa Provinsi Sulawesi Tenggara</span>
            <div class="security-badge">
                <span class="security-icon">🔒</span>
                <span>Sistem Aman & Terenkripsi</span>
            </div>
        </div>
    </div>

    <!-- Password Toggle Script -->
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleButton = document.querySelector('.password-toggle');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleButton.textContent = 'Sembunyikan';
            } else {
                passwordInput.type = 'password';
                toggleButton.textContent = 'Tampilkan';
            }
        }

        // Auto-hide error message after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const errorMessage = document.querySelector('.error-message');
            if (errorMessage) {
                setTimeout(() => {
                    errorMessage.style.opacity = '0';
                    errorMessage.style.transition = 'opacity 0.5s ease';
                    setTimeout(() => {
                        errorMessage.style.display = 'none';
                    }, 500);
                }, 5000);
            }
        });
    </script>
</body>
</html>
