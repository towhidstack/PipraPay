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

$storagePath = __DIR__ . '/pp-media/storage';
$storageReal = is_dir($storagePath) ? (realpath($storagePath) ?: $storagePath) : $storagePath;
$storageProbe = false;
$storageProbeFile = rtrim($storageReal, '/') . '/.health-probe';

if (is_dir($storageReal) || @mkdir($storageReal, 0777, true)) {
    $storageProbe = @file_put_contents($storageProbeFile, 'ok') !== false;
    if ($storageProbe) {
        @unlink($storageProbeFile);
    }
}

$phpUser = 'unknown';
if (function_exists('posix_getpwuid') && function_exists('posix_geteuid')) {
    $pw = posix_getpwuid(posix_geteuid());
    if (is_array($pw) && ! empty($pw['name'])) {
        $phpUser = (string) $pw['name'];
    }
}

$runtime = 'unknown';
if (is_file('/etc/supervisor/conf.d/supervisord.conf')) {
    $runtime = 'dockerfile';
} elseif (is_file('/assets/scripts/prestart.mjs')) {
    $runtime = 'nixpacks';
}

$bootstrapMarker = rtrim($storageReal, '/') . '/.piprapay-perms-ok';
$bootstrapOk = is_file($bootstrapMarker);

echo json_encode([
    'ok' => extension_loaded('imagick') && $storageProbe,
    'build' => $buildVersion,
    'php' => PHP_VERSION,
    'imagick' => extension_loaded('imagick') ? 'enabled' : 'disabled',
    'database' => $dbOk === null ? 'not_configured' : ($dbOk ? 'connected' : 'failed'),
    'database_error' => $dbError,
    'sapi' => PHP_SAPI,
    'php_user' => $phpUser,
    'runtime' => $runtime,
    'bootstrap_permissions_ok' => $bootstrapOk,
    'storage_path' => $storageReal,
    'storage_exists' => is_dir($storageReal),
    'storage_writable_probe' => $storageProbe,
    'storage_is_writable_flag' => is_dir($storageReal) ? is_writable($storageReal) : false,
    'hint' => $storageProbe ? null : (
        $phpUser === 'nobody'
            ? 'Nixpacks detected (php_user=nobody). Mount named volume at /app/pp-media/storage and redeploy; prefer Dockerfile build on Dokploy.'
            : 'Mount named volume at /app/pp-media/storage and redeploy PipraPay.'
    ),
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
