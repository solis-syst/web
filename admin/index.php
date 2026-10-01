<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/config.php';
require_admin();

$pdo = db();
$total = (int)$pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
$active = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE status = 'active'")->fetchColumn();
$banned = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE status = 'banned'")->fetchColumn();
$premium = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE premium_plan <> 'free' AND premium_until IS NOT NULL AND premium_until > UTC_TIMESTAMP()")->fetchColumn();

$users = $pdo->query(
    'SELECT id, installation_id, app_version, os, arch, country, status, premium_plan, premium_until, last_seen_at
     FROM users ORDER BY last_seen_at DESC LIMIT 100'
)->fetchAll();
?>
<!doctype html>
<html lang="en">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Solis Admin</title><link rel="stylesheet" href="/admin/style.css"></head>
<body>
<header><div><strong>Solis Admin</strong><span>Telemetry & user management</span></div><a href="/admin/logout.php">Sign out</a></header>
<main>
<section class="stats">
<div><b><?= $total ?></b><span>Total users</span></div>
<div><b><?= $active ?></b><span>Active</span></div>
<div><b><?= $premium ?></b><span>Premium</span></div>
<div><b><?= $banned ?></b><span>Banned</span></div>
</section>
<section class="card">
<div class="section-head"><h2>Users</h2><a href="/admin/events.php">Telemetry events</a></div>
<div class="table-wrap"><table><thead><tr><th>Installation</th><th>Version</th><th>Platform</th><th>Country</th><th>Status</th><th>Premium</th><th>Last seen</th><th></th></tr></thead><tbody>
<?php foreach ($users as $u): ?>
<tr>
<td><code><?= htmlspecialchars($u['installation_id']) ?></code></td>
<td><?= htmlspecialchars($u['app_version'] ?: '-') ?></td>
<td><?= htmlspecialchars(($u['os'] ?: '-') . ' / ' . ($u['arch'] ?: '-')) ?></td>
<td><?= htmlspecialchars($u['country'] ?: '-') ?></td>
<td><span class="status <?= htmlspecialchars($u['status']) ?>"><?= htmlspecialchars($u['status']) ?></span></td>
<td><?= htmlspecialchars($u['premium_plan']) ?><?= $u['premium_until'] ? '<br><small>' . htmlspecialchars($u['premium_until']) . '</small>' : '' ?></td>
<td><?= htmlspecialchars($u['last_seen_at']) ?></td>
<td><a href="/admin/user.php?id=<?= (int)$u['id'] ?>">Manage</a></td>
</tr>
<?php endforeach; ?>
</tbody></table></div>
</section>
</main>
</body>
</html>
