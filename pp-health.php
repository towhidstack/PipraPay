<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

$buildVersion = is_file(__DIR__ . '/BUILD_VERSION')
    ? trim((string) file_get_contents(__DIR__ . '/BUILD_VERSION'))
    : 'unknown';

$dbOk = null;
$dbError = null;

if (is_file(__DIR__ . '/pp-config.php')) {
    require __DIR__ . '/pp-config.php';
    $host = ($db_host ?? '') === 'localhost' ? '127.0.0.1' : ($db_host ?? '');
    try {
        $pdo = new PDO(
            'mysql:host=' . $host . ';port=' . ($db_port ?? 3306) . ';dbname=' . ($db_name ?? ''),
            $db_user ?? '',
            $db_pass ?? '',
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        $dbOk = true;
    } catch (Throwable $e) {
        $dbOk = false;
        $dbError = $e->getMessage();
    }
}

echo json_encode([
    'ok' => extension_loaded('imagick'),
    'build' => $buildVersion,
    'php' => PHP_VERSION,
    'imagick' => extension_loaded('imagick') ? 'enabled' : 'disabled',
    'database' => $dbOk === null ? 'not_configured' : ($dbOk ? 'connected' : 'failed'),
    'database_error' => $dbError,
    'sapi' => PHP_SAPI,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
