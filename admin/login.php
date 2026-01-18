<?php
/**
 * Dızo Wear - Admin Login
 */

session_start();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/models/User.php';

// Zaten giriş yapmışsa dashboard'a yönlendir
if (isset($_SESSION['admin'])) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // CSRF kontrolü
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
        $error = 'Güvenlik hatası. Lütfen sayfayı yenileyip tekrar deneyin.';
    } else {
        $userModel = new User();
        $user = $userModel->authenticate($email, $password);
        
        if ($user && in_array($user['role'], ['admin', 'superadmin'])) {
            $_SESSION['admin'] = $user;
            header('Location: index.php');
            exit;
        } else {
            $error = 'E-posta veya şifre hatalı, ya da yetkiniz yok.';
        }
    }
}

// CSRF token oluştur
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Giriş | Dızo Wear</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Inter', sans-serif; 
            background: linear-gradient(135deg, #1a1a1a 0%, #000 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container { width: 100%; max-width: 400px; padding: 20px; }
        .login-card { 
            background: #fff; 
            border-radius: 16px; 
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .logo { font-size: 28px; font-weight: 800; text-align: center; margin-bottom: 10px; }
        .logo span { color: #666; }
        .admin-badge { 
            text-align: center; 
            margin-bottom: 30px;
            color: #666;
            font-size: 14px;
        }
        .form-control { 
            border-radius: 8px; 
            padding: 12px 16px;
            border: 2px solid #eee;
            transition: all 0.3s;
        }
        .form-control:focus { 
            border-color: #000; 
            box-shadow: none;
        }
        .btn-login {
            background: #000;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 14px;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s;
        }
        .btn-login:hover { background: #333; color: #fff; }
        .alert { border-radius: 8px; }
        .back-link { 
            display: block; 
            text-align: center; 
            margin-top: 20px;
            color: #666;
            text-decoration: none;
        }
        .back-link:hover { color: #000; }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="logo">DIZO<span>WEAR</span></div>
            <div class="admin-badge">
                <i class="bi bi-shield-lock me-1"></i> Admin Panel
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-danger mb-4">
                    <i class="bi bi-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                
                <div class="mb-3">
                    <label class="form-label">E-posta</label>
                    <input type="email" name="email" class="form-control" required 
                           placeholder="admin@dizowear.com">
                </div>
                
                <div class="mb-4">
                    <label class="form-label">Şifre</label>
                    <input type="password" name="password" class="form-control" required
                           placeholder="••••••••">
                </div>
                
                <button type="submit" class="btn btn-login">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Giriş Yap
                </button>
            </form>
            
            <a href="../" class="back-link">
                <i class="bi bi-arrow-left me-1"></i>Siteye Dön
            </a>
        </div>
    </div>
</body>
</html>
