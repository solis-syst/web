<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/config.php';
require_admin();

$pdo = db();
$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
if ($id < 1) { header('Location: /admin/'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = (string)($_POST['action'] ?? '');
    $adminId = (int)$_SESSION['admin_id'];

    if ($action === 'ban' || $action === 'unban') {
        $status = $action === 'ban' ? 'banned' : 'active';
        $pdo->prepare('UPDATE users SET status = ? WHERE id = ?')->execute([$status, $id]);
        $pdo->prepare('INSERT INTO admin_audit_log (admin_id, user_id, action, details, created_at) VALUES (?, ?, ?, ?, UTC_TIMESTAMP())')
            ->execute([$adminId, $id, $action, null]);
    } elseif ($action === 'premium') {
        $plan = in_array($_POST['plan'] ?? '', ['monthly','yearly','lifetime'], true) ? $_POST['plan'] : 'monthly';
        $days = ['monthly'=>30,'yearly'=>365,'lifetime'=>36500][$plan];
        $until = $plan === 'lifetime' ? '2099-12-31 23:59:59' : gmdate('Y-m-d H:i:s', time() + $days * 86400);
        $pdo->prepare('UPDATE users SET premium_plan = ?, premium_until = ? WHERE id = ?')->execute([$plan, $until, $id]);
        $pdo->prepare('INSERT INTO admin_audit_log (admin_id, user_id, action, details, created_at) VALUES (?, ?, ?, ?, UTC_TIMESTAMP())')
            ->execute([$adminId, $id, 'premium_granted', $plan]);
    } elseif ($action === 'remove_premium') {
        $pdo->prepare("UPDATE users SET premium_plan = 'free', premium_until = NULL WHERE id = ?")->execute([$id]);
        $pdo->prepare('INSERT INTO admin_audit_log (admin_id, user_id, action, details, created_at) VALUES (?, ?, ?, ?, UTC_TIMESTAMP())')
            ->execute([$adminId, $id, 'premium_removed', null]);
    }

    header('Location: /admin/user.php?id=' . $id);
    exit;
}

$stmt = $pdo->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
$stmt->execute([$id]);
$user = $stmt->fetch();
if (!$user) { header('Location: /admin/'); exit; }

$events = $pdo->prepare('SELECT event_name, app_version, metadata_json, created_at FROM telemetry_events WHERE user_id = ? ORDER BY created_at DESC LIMIT 50');
$events->execute([$id]);
$events = $events->fetchAll();
?>
<!doctype html>
<html lang="en">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>User · Solis Admin</title><link rel="stylesheet" href="/admin/style.css"></head>
<body>
<header><div><strong>User management</strong><span><?= htmlspecialchars($user['installation_id']) ?></span></div><a href="/admin/">Back</a></header>
<main>
<section class="card">
<h2>Account</h2>
<div class="details">
<p><b>Status:</b> <?= htmlspecialchars($user['status']) ?></p>
<p><b>Premium:</b> <?= htmlspecialchars($user['premium_plan']) ?><?= $user['premium_until'] ? ' until ' . htmlspecialchars($user['premium_until']) : '' ?></p>
<p><b>Version:</b> <?= htmlspecialchars($user['app_version'] ?: '-') ?></p>
<p><b>OS:</b> <?= htmlspecialchars($user['os'] ?: '-') ?></p>
<p><b>Architecture:</b> <?= htmlspecialchars($user['arch'] ?: '-') ?></p>
<p><b>Country:</b> <?= htmlspecialchars($user['country'] ?: '-') ?></p>
<p><b>Last seen:</b> <?= htmlspecialchars($user['last_seen_at']) ?></p>
</div>
<div class="actions">
<form method="post"><input type="hidden" name="id" value="<?= $id ?>"><input type="hidden" name="action" value="<?= $user['status'] === 'banned' ? 'unban' : 'ban' ?>"><button><?= $user['status'] === 'banned' ? 'Unban user' : 'Ban user' ?></button></form>
<form method="post"><input type="hidden" name="id" value="<?= $id ?>"><input type="hidden" name="action" value="premium"><select name="plan"><option value="monthly">Monthly</option><option value="yearly">Yearly</option><option value="lifetime">Lifetime</option></select><button>Grant premium</button></form>
<form method="post"><input type="hidden" name="id" value="<?= $id ?>"><input type="hidden" name="action" value="remove_premium"><button class="danger">Remove premium</button></form>
</div>
</section>
<section class="card"><h2>Recent activity</h2><div class="table-wrap"><table><thead><tr><th>Event</th><th>Version</th><th>Metadata</th><th>Time</th></tr></thead><tbody>
<?php foreach ($events as $e): ?><tr><td><?= htmlspecialchars($e['event_name']) ?></td><td><?= htmlspecialchars($e['app_version']) ?></td><td><code><?= htmlspecialchars($e['metadata_json']) ?></code></td><td><?= htmlspecialchars($e['created_at']) ?></td></tr><?php endforeach; ?>
</tbody></table></div></section>
</main>
</body>
</html>
