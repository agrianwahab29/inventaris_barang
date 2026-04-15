<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Aplikasi Inventaris Barang</title>

    <!-- Favicon - Logo Tut Wuri Handayani -->
    <link rel="icon" type="image/png" href="<?php echo e(asset('image/tut-wuri-handayani.png')); ?>?v=4">
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo e(asset('favicon.ico')); ?>?v=4">
    <link rel="icon" type="image/svg+xml" href="<?php echo e(asset('image/tut-wuri-handayani.svg')); ?>?v=4">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <style>
        * {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
        }

        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            width: 100%;
            max-width: 450px;
        }

        .login-header {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            padding: 32px 24px;
            text-align: center;
            color: white;
        }

        .login-header i {
            font-size: 3rem;
            margin-bottom: 12px;
            opacity: 0.9;
        }

        .login-header h4 {
            font-size: 1.25rem;
            font-weight: 700;
        }

        .login-header p {
            font-size: 0.8125rem;
        }

        .login-body {
            padding: 28px 24px;
        }

        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            transition: all 0.3s;
            font-size: 16px; /* Prevents iOS zoom on focus */
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .form-label {
            font-size: 0.875rem;
            font-weight: 600;
        }

        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s;
            font-size: 0.9375rem;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }

        .input-group-text {
            background: transparent;
            border: 2px solid #e0e0e0;
            border-right: none;
            color: #667eea;
        }

        .input-group .form-control {
            border-left: none;
        }

        .input-group:focus-within .input-group-text {
            border-color: #667eea;
        }

        /* Mobile adjustments */
        @media (max-width: 480px) {
            body {
                padding: 12px;
                align-items: flex-start;
                padding-top: 10vh;
            }

            .login-card {
                border-radius: 16px;
            }

            .login-header {
                padding: 24px 16px;
            }

            .login-header i {
                font-size: 2.5rem;
                margin-bottom: 8px;
            }

            .login-header h4 {
                font-size: 1.125rem;
            }

            .login-body {
                padding: 20px 16px;
            }

            .mb-4 {
                margin-bottom: 1rem !important;
            }
        }

        /* Very small screens (iPhone SE, 320px) */
        @media (max-width: 360px) {
            body {
                padding: 8px;
                padding-top: 5vh;
            }

            .login-header {
                padding: 20px 12px;
            }

            .login-body {
                padding: 16px 12px;
            }
        }
    </style>
</head>

<body>
    <div class="login-card">
        <div class="login-header">
            <i class="fas fa-boxes"></i>
            <h4 class="mb-1">Aplikasi Inventaris</h4>
            <p class="mb-0 opacity-75">Sistem Manajemen Barang Kantor</p>
        </div>

        <div class="login-body">
            <?php if($errors->any()): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i><?php echo e($errors->first()); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo e(route('login')); ?>">
                <?php echo csrf_field(); ?>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Username</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" name="username" class="form-control" placeholder="Masukkan username" required
                            autofocus>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" name="password" class="form-control" placeholder="Masukkan password"
                            required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-login w-100">
                    <i class="fas fa-sign-in-alt me-2"></i>Masuk
                </button>
            </form>

        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html><?php /**PATH C:\laragon\www\inventaris-barang2\inventaris-kantor\resources\views/auth/login.blade.php ENDPATH**/ ?>