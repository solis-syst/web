<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/config.php';
start_secure_session();

if (!empty($_SESSION['admin_id'])) {
    header('Location: /admin/');
    exit;
}

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim((string)($_POST['email'] ?? ''));
    $password = (string)($_POST['password'] ?? '');

    $stmt = db()->prepare('SELECT id, password_hash FROM admin_users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['admin_id'] = (int)$admin['id'];
        header('Location: /admin/');
        exit;
    }

    $error = 'Invalid credentials.';
}
?>
<!doctype html>
<html lang="en">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Solis Admin</title><link rel="stylesheet" href="/admin/style.css"></head>
<body class="auth">
<form method="post" class="card">
<h1>Solis Admin</h1>
<p>Sign in to manage users and telemetry.</p>
<?php if ($error): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
<label>Email<input type="email" name="email" required autocomplete="username"></label>
<label>Password<input type="password" name="password" required autocomplete="current-password"></label>
<button type="submit">Sign in</button>
</form>
</body>
</html>
