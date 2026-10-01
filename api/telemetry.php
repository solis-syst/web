<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['ok' => false, 'error' => 'method_not_allowed'], 405);
}

$data = request_json();
$installationId = trim((string)($data['installation_id'] ?? ''));
$event = trim((string)($data['event'] ?? ''));
$version = trim((string)($data['app_version'] ?? ''));
$os = trim((string)($data['os'] ?? ''));
$arch = trim((string)($data['arch'] ?? ''));
$country = strtoupper(trim((string)($data['country'] ?? '')));

if ($installationId === '' || strlen($installationId) > 128 || $event === '' || strlen($event) > 100) {
    json_response(['ok' => false, 'error' => 'invalid_payload'], 400);
}

if (!preg_match('/^[A-Za-z0-9._:-]+$/', $installationId)) {
    json_response(['ok' => false, 'error' => 'invalid_installation_id'], 400);
}

$pdo = db();

$stmt = $pdo->prepare(
    'INSERT INTO users (installation_id, app_version, os, arch, country, last_seen_at)
     VALUES (?, ?, ?, ?, ?, UTC_TIMESTAMP())
     ON DUPLICATE KEY UPDATE
       app_version = VALUES(app_version),
       os = VALUES(os),
       arch = VALUES(arch),
       country = VALUES(country),
       last_seen_at = UTC_TIMESTAMP()'
);
$stmt->execute([$installationId, substr($version, 0, 32), substr($os, 0, 64), substr($arch, 0, 32), substr($country, 0, 2)]);

$user = $pdo->prepare('SELECT id FROM users WHERE installation_id = ? LIMIT 1');
$user->execute([$installationId]);
$userId = (int)$user->fetchColumn();

$metadata = $data['metadata'] ?? [];
if (!is_array($metadata)) $metadata = [];

$stmt = $pdo->prepare(
    'INSERT INTO telemetry_events (user_id, event_name, app_version, metadata_json, ip_hash, created_at)
     VALUES (?, ?, ?, ?, SHA2(CONCAT(?, :salt), 256), UTC_TIMESTAMP())'
);
$stmt->execute([
    $userId,
    substr($event, 0, 100),
    substr($version, 0, 32),
    json_encode($metadata, JSON_UNESCAPED_SLASHES),
    client_ip(),
    ':salt' => getenv('SOLIS_IP_SALT') ?: 'CHANGE_IP_SALT'
]);

json_response(['ok' => true]);
