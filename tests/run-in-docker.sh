#!/usr/bin/env bash
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
cd "$ROOT"

PHP_IMAGE="${PHP_IMAGE:-php@sha256:42ffbc0798e4449bbd1e14fc4dcb87774aa1ad1900a09ef6a965bc0880aa2161}"
COMPOSER_SHA256="8b3f41253363f0645402d1951d6e7f02adedeef29c16de6074763e463e25c23f"

ORDER_EXTRA=("$@")

sudo docker run --rm \
	-e "COMPOSER_SHA256=${COMPOSER_SHA256}" \
	-v "$ROOT:/app" -w /app "$PHP_IMAGE" \
	bash -lc '
set -euo pipefail
export DEBIAN_FRONTEND=noninteractive
if ! apt-get update; then
  sed -i "s|deb.debian.org|archive.debian.org|g" /etc/apt/sources.list
  sed -i "s|security.debian.org|archive.debian.org|g" /etc/apt/sources.list
  echo "Acquire::Check-Valid-Until \"false\";" > /etc/apt/apt.conf.d/99no-check-valid
  apt-get update
fi
if php -r "exit(version_compare(PHP_VERSION, \"8.0.0\", \">=\") ? 0 : 1);"; then
  PHPUNIT_URL="https://phar.phpunit.de/phpunit-9.6.22.phar"
  PHPUNIT_SHA256="9618d52015c9b06b4979a8e481ca9567be6be20e711e98926c61378a400e1f2e"
  PHPUNIT_CONFIG="phpunit-9.xml.dist"
else
  PHPUNIT_URL="https://phar.phpunit.de/phpunit-8.5.41.phar"
  PHPUNIT_SHA256="df3a284a28ef7eee387707b6cfd6e569f27698c557281c440addaee165c64c16"
  PHPUNIT_CONFIG="phpunit.xml.dist"
fi
if [ ! -x /usr/local/bin/phpunit ] || [ ! -f /tmp/composer.phar ]; then
  apt-get install -y --no-install-recommends unzip git libxml2-dev libonig-dev
  docker-php-ext-install mbstring
  if ! php -m | grep -q "^dom$"; then docker-php-ext-install dom; fi
  curl -fsSL -o /tmp/composer.phar https://getcomposer.org/download/2.2.25/composer.phar
  echo "${COMPOSER_SHA256}  /tmp/composer.phar" | sha256sum -c -
  curl -fsSL -o /usr/local/bin/phpunit "${PHPUNIT_URL}"
  echo "${PHPUNIT_SHA256}  /usr/local/bin/phpunit" | sha256sum -c -
  chmod +x /usr/local/bin/phpunit
fi
if php -r "exit(version_compare(PHP_VERSION, \"8.0.0\", \">=\") ? 0 : 1);"; then
  php tests/filter-composer-php83.php
  if [ ! -f vendor/.installed-php83 ]; then
    rm -rf vendor composer.lock
    COMPOSER=/tmp/composer.php83.json php /tmp/composer.phar update --no-interaction --prefer-dist
    rm -f vendor/.installed-php72
    touch vendor/.installed-php83
  fi
else
  if [ ! -f vendor/.installed-php72 ]; then
    rm -rf vendor composer.lock
    php /tmp/composer.phar update --no-interaction --prefer-dist
    rm -f vendor/.installed-php83
    touch vendor/.installed-php72
  fi
fi
php -r "echo PHP_VERSION, PHP_EOL;"
if php -r "exit(version_compare(PHP_VERSION, \"8.0.0\", \">=\") ? 0 : 1);"; then
  php -d error_reporting=24575 /usr/local/bin/phpunit --configuration "${PHPUNIT_CONFIG}" --do-not-cache-result "$@"
else
  phpunit --configuration "${PHPUNIT_CONFIG}" --do-not-cache-result "$@"
fi
' _ "${ORDER_EXTRA[@]}"
