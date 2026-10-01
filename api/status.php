<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['ok' => false, 'error' => 'method_not_allowed'], 405);
}

$data = request_json();
$installationId = trim((string)($data['installation_id'] ?? ''));

if ($installationId === '') {
    json_response(['ok' => false, 'error' => 'installation_id_required'], 400);
}

$stmt = db()->prepare(
    'SELECT status, premium_plan, premium_until
     FROM users WHERE installation_id = ? LIMIT 1'
);
$stmt->execute([$installationId]);
$user = $stmt->fetch();

if (!$user) {
    json_response([
        'ok' => true,
        'status' => 'active',
        'premium' => false,
        'premium_plan' => 'free',
        'premium_until' => null
    ]);
}

$premium = $user['premium_plan'] !== 'free'
    && $user['premium_until'] !== null
    && strtotime((string)$user['premium_until']) > time()
    && $user['status'] === 'active';

json_response([
    'ok' => true,
    'status' => $user['status'],
    'premium' => $premium,
    'premium_plan' => $user['premium_plan'],
    'premium_until' => $user['premium_until']
]);
