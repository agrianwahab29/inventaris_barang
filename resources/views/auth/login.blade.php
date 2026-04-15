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
        /* OpenCode Design System - Terminal Aesthetic */
        :root {
            /* Primary Colors - Warm Dark */
            --bg-primary: #201d1d;
            --bg-elevated: #302c2c;
            --bg-surface: #252222;
            
            /* Text Colors - Warm Off-White */
            --text-primary: #fdfcfc;
            --text-secondary: #9a9898;
            --text-muted: #6e6e73;
            
            /* Accent Colors */
            --accent-blue: #007aff;
            --accent-red: #ff3b30;
            --border-warm: rgba(15, 0, 0, 0.12);
            --border-subtle: #646262;
            --border-hover: #9a9898;
            
            /* Input Colors */
            --input-bg: #f8f7f7;
            --input-text: #201d1d;
            --input-placeholder: #6e6e73;
            --input-border: rgba(15, 0, 0, 0.12);
            
            /* Typography */
            --font-mono: 'Berkeley Mono', 'IBM Plex Mono', 'ui-monospace', 'SFMono-Regular', 'Menlo', 'Monaco', 'Cascadia Mono', 'Segoe UI Mono', 'Roboto Mono', 'Oxygen Mono', 'Ubuntu Monospace', 'Source Code Pro', 'Fira Mono', 'Droid Sans Mono', 'Courier New', monospace;
        }

        /* Reset */
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
            font-family: var(--font-mono);
            background-color: var(--bg-primary);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 24px;
            line-height: 1.5;
        }

        /* Brand Section */
        .brand {
            text-align: center;
            margin-bottom: 48px;
        }

        .brand-logo {
            font-size: 28px;
            font-weight: 700;
            color: var(--text-primary);
            letter-spacing: 0.1em;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
        }

        .brand-prompt {
            color: var(--accent-blue);
            font-weight: 400;
        }

        .brand-subtitle {
            font-size: 14px;
            font-weight: 400;
            color: var(--text-secondary);
            letter-spacing: 0.05em;
        }

        /* ASCII Art Divider */
        .ascii-divider {
            color: var(--text-muted);
            font-size: 12px;
            margin: 16px 0;
            letter-spacing: 2px;
            opacity: 0.5;
        }

        /* Login Card */
        .login-card {
            background-color: var(--bg-elevated);
            border: 1px solid var(--border-warm);
            border-radius: 4px;
            width: 100%;
            max-width: 420px;
            padding: 48px;
        }

        /* Error Message */
        .error-message {
            background-color: rgba(255, 59, 48, 0.1);
            border: 1px solid rgba(255, 59, 48, 0.3);
            border-radius: 4px;
            padding: 12px 16px;
            margin-bottom: 24px;
            font-size: 14px;
            font-weight: 500;
            color: var(--accent-red);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .error-icon {
            font-size: 16px;
            flex-shrink: 0;
        }

        /* Form Elements */
        .form-group {
            margin-bottom: 24px;
        }

        .form-label {
            display: block;
            font-size: 16px;
            font-weight: 500;
            color: var(--text-secondary);
            margin-bottom: 8px;
        }

        .form-input {
            width: 100%;
            background-color: var(--input-bg);
            border: 1px solid var(--input-border);
            border-radius: 6px;
            padding: 16px 20px;
            font-family: var(--font-mono);
            font-size: 16px;
            font-weight: 400;
            color: var(--input-text);
            transition: border-color 0.2s ease;
            -webkit-appearance: none;
            appearance: none;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--accent-blue);
        }

        .form-input::placeholder {
            color: var(--input-placeholder);
        }

        /* Password visibility toggle */
        .password-wrapper {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--input-placeholder);
            cursor: pointer;
            font-family: var(--font-mono);
            font-size: 12px;
            padding: 4px 8px;
            transition: color 0.2s ease;
        }

        .password-toggle:hover {
            color: var(--input-text);
        }

        /* Form Options */
        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 32px;
            font-size: 14px;
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
            border: 1px solid var(--border-subtle);
            border-radius: 3px;
            background-color: transparent;
            appearance: none;
            cursor: pointer;
            position: relative;
            transition: all 0.2s ease;
        }

        .checkbox-input:checked {
            background-color: var(--accent-blue);
            border-color: var(--accent-blue);
        }

        .checkbox-input:checked::after {
            content: '✓';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: var(--text-primary);
            font-size: 12px;
            font-weight: 700;
        }

        .checkbox-label {
            color: var(--text-secondary);
            font-weight: 400;
        }

        .forgot-link {
            color: var(--accent-blue);
            text-decoration: underline;
            text-underline-offset: 3px;
            transition: opacity 0.2s ease;
        }

        .forgot-link:hover {
            opacity: 0.8;
        }

        /* Submit Button */
        .btn-submit {
            width: 100%;
            background-color: var(--bg-primary);
            border: 1px solid var(--border-subtle);
            border-radius: 4px;
            padding: 12px 20px;
            font-family: var(--font-mono);
            font-size: 16px;
            font-weight: 500;
            color: var(--text-primary);
            line-height: 2;
            cursor: pointer;
            transition: all 0.2s ease;
            -webkit-appearance: none;
            appearance: none;
        }

        .btn-submit:hover {
            background-color: var(--bg-elevated);
            border-color: var(--border-hover);
        }

        .btn-submit:active {
            background-color: var(--bg-primary);
            border-color: var(--text-primary);
        }

        .btn-submit:focus {
            outline: none;
            border-color: var(--accent-blue);
        }

        /* Footer */
        .login-footer {
            margin-top: 48px;
            text-align: center;
            font-size: 13px;
            color: var(--text-muted);
        }

        .footer-divider {
            color: var(--text-muted);
            margin: 0 8px;
        }

        /* Responsive Adjustments */
        @media (max-width: 480px) {
            body {
                padding: 16px;
                justify-content: flex-start;
                padding-top: 10vh;
            }

            .brand {
                margin-bottom: 32px;
            }

            .brand-logo {
                font-size: 24px;
            }

            .brand-subtitle {
                font-size: 13px;
            }

            .login-card {
                padding: 32px 24px;
            }

            .form-group {
                margin-bottom: 20px;
            }

            .form-input {
                padding: 14px 16px;
            }

            .form-options {
                flex-direction: column;
                gap: 12px;
                align-items: flex-start;
                margin-bottom: 24px;
            }
        }

        @media (max-width: 360px) {
            body {
                padding: 12px;
            }

            .login-card {
                padding: 24px 20px;
            }

            .brand-logo {
                font-size: 22px;
                letter-spacing: 0.05em;
            }

            .ascii-divider {
                font-size: 10px;
                letter-spacing: 1px;
            }
        }

        /* Selection color */
        ::selection {
            background-color: var(--accent-blue);
            color: var(--text-primary);
        }
    </style>
</head>

<body>
    <!-- Brand Header -->
    <div class="brand">
        <div class="brand-logo">
            <span class="brand-prompt">>_</span>
            <span>INVENTARIS</span>
        </div>
        <div class="ascii-divider">┌─────────────────────────┐</div>
        <p class="brand-subtitle">Sistem Manajemen Barang</p>
    </div>

    <!-- Login Card -->
    <div class="login-card">
        <!-- Error Messages -->
        @if($errors->any())
        <div class="error-message" role="alert">
            <span class="error-icon">✗</span>
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
                    <button type="button" class="password-toggle" onclick="togglePassword()" aria-label="Toggle password visibility">
                        [show]
                    </button>
                </div>
            </div>

            <!-- Form Options -->
            <div class="form-options">
                <label class="checkbox-wrapper">
                    <input type="checkbox" name="remember" class="checkbox-input" id="remember">
                    <span class="checkbox-label">Ingat saya</span>
                </label>
                <a href="#" class="forgot-link">Lupa password?</a>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn-submit">
                Masuk →
            </button>
        </form>
    </div>

    <!-- Footer -->
    <div class="login-footer">
        <span>© 2026</span>
        <span class="footer-divider">|</span>
        <span>Balai Bahasa</span>
    </div>

    <!-- Password Toggle Script -->
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleButton = document.querySelector('.password-toggle');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleButton.textContent = '[hide]';
            } else {
                passwordInput.type = 'password';
                toggleButton.textContent = '[show]';
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
