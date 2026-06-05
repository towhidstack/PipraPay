<?php
declare(strict_types=1);

define('PipraPay_INIT', true);

require __DIR__ . '/../pp-content/pp-include/pp-functions.php';

if (piprapay_bootstrap_config_from_env()) {
    if (getenv('PIPRAPAY_BOOTSTRAP_VERBOSE') === '1') {
        fwrite(STDOUT, "[piprapay] pp-config.php ready (env or volume)\n");
    }
    exit(0);
}

fwrite(STDERR, "[piprapay] pp-config.php not created — check PIPRAPAY_AUTO_DB_CONFIG and DB_* env\n");
exit(0);
