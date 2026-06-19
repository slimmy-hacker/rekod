#!/bin/sh
set -e

echo "=== Rekod container starting ==="
echo "=== PHP version: $(php --version | head -1) ==="
echo "=== APP_KEY set: $([ -n "$APP_KEY" ] && echo YES || echo NO) ==="

echo "=== DIAGNOSTIC: bootstrap/app.php first 20 bytes (hex) ==="
xxd /var/www/bootstrap/app.php | head -2

echo "=== DIAGNOSTIC: PHP syntax check ==="
php -l /var/www/bootstrap/app.php 2>&1 || echo "SYNTAX ERROR DETECTED"

echo "=== DIAGNOSTIC: Direct PHP boot test ==="
php -r "
define('LARAVEL_START', microtime(true));
require '/var/www/vendor/autoload.php';
echo 'Autoload: OK' . PHP_EOL;
try {
    \$app = require_once '/var/www/bootstrap/app.php';
    echo 'App class: ' . get_class(\$app) . PHP_EOL;
} catch (\Throwable \$e) {
    echo 'Boot error: ' . \$e->getMessage() . PHP_EOL;
    echo 'In file: ' . \$e->getFile() . ':' . \$e->getLine() . PHP_EOL;
}
" 2>&1

echo "=== DIAGNOSTIC: Done ==="

echo "=== Starting server on port ${PORT:-80} ==="
php artisan serve --host=0.0.0.0 --port=${PORT:-80}

