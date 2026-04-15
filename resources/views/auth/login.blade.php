<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Inventaris Barang</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('image/tut-wuri-handayani.png') }}?v=4">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v=4">

    <style>
        /* Elegant Government Design System */
        :root {
            /* Primary Colors */
            --primary-blue: #1e4d8c;
            --primary-blue-dark: #163d70;
            --primary-blue-light: #2d6bc4;
            --accent-gold: #c9a227;
            --accent-gold-light: #d4b84a;
            
            /* Background Colors */
            --bg-primary: #f8fafc;
            --bg-secondary: #f1f5f9;
            --card-bg: #ffffff;
            --card-border: #e2e8f0;
            
            /* Text Colors */
            --text-primary: #0f172a;
            --text-secondary: #475569;
            --text-muted: #64748b;
            --text-white: #ffffff;
            
            /* Status Colors */
            --error-red: #dc2626;
            --error-bg: #fef2f2;
            
            /* Typography */
            --font-primary: system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            --font-heading: Georgia, 'Times New Roman', serif;
        }

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
            background: linear-gradient(180deg, var(--bg-primary) 0%, var(--bg-secondary) 100%);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
            line-height: 1.6;
        }

        .login-container {
            width: 100%;
            max-width: 440px;
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Logo Section */
        .logo-section {
            text-align: center;
            margin-bottom: 28px;
        }

        .logo {
            width: 100px;
            height: 100px;
            object-fit: contain;
            margin-bottom: 20px;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.08));
        }

        /* Header Text */
        .header-title {
            font-family: var(--font-heading);
            font-size: 20px;
            font-weight: 600;
            color: var(--text-primary);
            line-height: 1.4;
            margin-bottom: 6px;
            letter-spacing: 0.3px;
        }

        .header-subtitle {
            font-size: 13px;
            color: var(--text-muted);
            font-weight: 400;
        }

        /* Divider */
        .divider {
            width: 50px;
            height: 3px;
            background: linear-gradient(90deg, var(--primary-blue) 0%, var(--accent-gold) 100%);
            margin: 16px auto;
            border-radius: 2px;
        }

        /* Login Card */
        .login-card {
            background-color: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 10px;
            padding: 32px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08), 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        /* Error Message */
        .error-message {
            background-color: var(--error-bg);
            border: 1px solid rgba(220, 38, 38, 0.2);
            border-radius: 6px;
            padding: 10px 14px;
            margin-bottom: 20px;
            font-size: 13px;
            color: var(--error-red);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .error-icon {
            font-size: 16px;
            flex-shrink: 0;
        }

        /* Form Elements */
        .form-group {
            margin-bottom: 18px;
        }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-input {
            width: 100%;
            background-color: #ffffff;
            border: 1.5px solid #cbd5e1;
            border-radius: 6px;
            padding: 12px 14px;
            font-family: var(--font-primary);
            font-size: 15px;
            color: var(--text-primary);
            transition: all 0.15s ease;
        }

        .form-input:hover {
            border-color: #94a3b8;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 3px rgba(30, 77, 140, 0.1);
        }

        .form-input::placeholder {
            color: #94a3b8;
        }

        /* Password Field */
        .password-wrapper {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            font-size: 12px;
            font-weight: 500;
            padding: 6px 10px;
            border-radius: 4px;
            transition: all 0.15s ease;
        }

        .password-toggle:hover {
            background-color: #f1f5f9;
            color: var(--primary-blue);
        }

        /* Checkbox */
        .form-options {
            margin-bottom: 20px;
        }

        .checkbox-wrapper {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        .checkbox-input {
            width: 18px;
            height: 18px;
            border: 1.5px solid #cbd5e1;
            border-radius: 4px;
            background-color: #ffffff;
            appearance: none;
            cursor: pointer;
            position: relative;
            transition: all 0.15s ease;
        }

        .checkbox-input:checked {
            background-color: var(--primary-blue);
            border-color: var(--primary-blue);
        }

        .checkbox-input:checked::after {
            content: '';
            position: absolute;
            top: 2px;
            left: 5px;
            width: 5px;
            height: 9px;
            border: solid white;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }

        .checkbox-label {
            font-size: 13px;
            color: var(--text-secondary);
            user-select: none;
        }

        /* Submit Button */
        .btn-submit {
            width: 100%;
            background-color: var(--primary-blue);
            border: none;
            border-radius: 6px;
            padding: 13px 20px;
            font-family: var(--font-primary);
            font-size: 15px;
            font-weight: 600;
            color: var(--text-white);
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 2px 4px rgba(30, 77, 140, 0.2);
        }

        .btn-submit:hover {
            background-color: var(--primary-blue-dark);
            box-shadow: 0 4px 8px rgba(30, 77, 140, 0.3);
            transform: translateY(-1px);
        }

        .btn-submit:active {
            transform: translateY(0);
            box-shadow: 0 2px 4px rgba(30, 77, 140, 0.2);
        }

        /* Footer */
        .login-footer {
            margin-top: 24px;
            text-align: center;
            font-size: 12px;
            color: var(--text-muted);
        }

        .footer-divider {
            margin: 0 6px;
            color: var(--accent-gold);
        }

        /* Responsive */
        @media (max-width: 480px) {
            body {
                padding: 16px;
                justify-content: flex-start;
                padding-top: 8vh;
            }

            .logo {
                width: 80px;
                height: 80px;
            }

            .header-title {
                font-size: 18px;
            }

            .login-card {
                padding: 24px 20px;
            }

            .form-input {
                font-size: 16px;
            }
        }

        @media (max-width: 360px) {
            .header-title {
                font-size: 16px;
            }

            .header-subtitle {
                font-size: 12px;
            }

            .login-card {
                padding: 20px 16px;
            }
        }

        ::selection {
            background-color: var(--primary-blue);
            color: var(--text-white);
        }
    </style>
</head>

<body>
    <div class="login-container">
        <!-- Logo Section -->
        <div class="logo-section">
            <img src="{{ asset('image/tut-wuri-handayani.png') }}" alt="Logo Kemendikdasmen" class="logo">
            
            <h1 class="header-title">Sistem Inventaris Barang<br>Balai Bahasa Provinsi Sulawesi Tenggara</h1>
            <p class="header-subtitle">Kementerian Pendidikan, Kebudayaan, Riset, dan Teknologi</p>
            <div class="divider"></div>
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
                        placeholder="Masukkan username" 
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
                            placeholder="Masukkan password"
                            required
                            autocomplete="current-password"
                        >
                        <button type="button" class="password-toggle" onclick="togglePassword()">
                            Tampilkan
                        </button>
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="form-options">
                    <label class="checkbox-wrapper">
                        <input type="checkbox" name="remember" class="checkbox-input" id="remember">
                        <span class="checkbox-label">Ingat saya</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn-submit">
                    Masuk
                </button>
            </form>
        </div>

        <!-- Footer -->
        <div class="login-footer">
            <span>&copy; 2026</span>
            <span class="footer-divider">|</span>
            <span>Balai Bahasa Provinsi Sulawesi Tenggara</span>
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
