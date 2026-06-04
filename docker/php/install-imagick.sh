#!/bin/bash
set -eux

git clone --depth 1 https://github.com/Imagick/imagick.git /tmp/imagick
cd /tmp/imagick
phpize
./configure
make -j"$(nproc)"
make install
docker-php-ext-enable imagick
cd /
rm -rf /tmp/imagick

php -m | grep -i imagick
php -r 'if (!extension_loaded("imagick")) { fwrite(STDERR, "imagick failed to load\n"); exit(1); }'
