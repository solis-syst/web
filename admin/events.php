<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/config.php';
require_admin();

$events = db()->query(
    'SELECT e.event_name, e.app_version, e.metadata_json, e.created_at, u.installation_id, u.country
     FROM telemetry_events e JOIN users u ON u.id = e.user_id
     ORDER BY e.created_at DESC LIMIT 500'
)->fetchAll();
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Telemetry · Solis Admin</title><link rel="stylesheet" href="/admin/style.css"></head>
<body><header><div><strong>Telemetry events</strong><span>Latest 500 events</span></div><a href="/admin/">Back</a></header><main><section class="card"><div class="table-wrap"><table><thead><tr><th>Event</th><th>Installation</th><th>Country</th><th>Version</th><th>Metadata</th><th>Time</th></tr></thead><tbody>
<?php foreach ($events as $e): ?><tr><td><?= htmlspecialchars($e['event_name']) ?></td><td><code><?= htmlspecialchars($e['installation_id']) ?></code></td><td><?= htmlspecialchars($e['country']) ?></td><td><?= htmlspecialchars($e['app_version']) ?></td><td><code><?= htmlspecialchars($e['metadata_json']) ?></code></td><td><?= htmlspecialchars($e['created_at']) ?></td></tr><?php endforeach; ?>
</tbody></table></div></section></main></body></html>
