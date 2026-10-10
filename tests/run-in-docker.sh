#!/usr/bin/env bash
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
cd "$ROOT"

PHP_IMAGE_72="php@sha256:42ffbc0798e4449bbd1e14fc4dcb87774aa1ad1900a09ef6a965bc0880aa2161"
PHP_IMAGE_83="php@sha256:aafe21201943a8a6e497ddfb471e2ce68ada76adede38c7d61fede8918b24319"

PHP_SELECTOR="${PHP_SELECTOR:-7.2}"
case "${PHP_SELECTOR}" in
	7.2|72) PHP_IMAGE="${PHP_IMAGE:-${PHP_IMAGE_72}}" ;;
	8.3|83) PHP_IMAGE="${PHP_IMAGE:-${PHP_IMAGE_83}}" ;;
	*)
		echo "PHP_SELECTOR must be 7.2 or 8.3 (got: ${PHP_SELECTOR})" >&2
		exit 1
		;;
esac

if [[ "${PHP_IMAGE}" != *@sha256:* ]]; then
	echo "PHP_IMAGE must be digest-pinned (image@sha256:...); got: ${PHP_IMAGE}" >&2
	exit 1
fi

COMPOSER_SHA256="8b3f41253363f0645402d1951d6e7f02adedeef29c16de6074763e463e25c23f"
FUEL_CORE_URL="https://api.github.com/repos/fuel/core/zipball/c82b10b8000363bdc6951f023e455c4862ad3fee"
FUEL_CORE_SHA256="26375739897a2655ad10228e8616f62d89cefc6bbb0ae78fd0c00991366769ff"

ORDER_EXTRA=("$@")

sudo docker run --rm \
	-e "COMPOSER_SHA256=${COMPOSER_SHA256}" \
	-e "FUEL_CORE_URL=${FUEL_CORE_URL}" \
	-e "FUEL_CORE_SHA256=${FUEL_CORE_SHA256}" \
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
install_fuel_core() {
  if [ -f fuel/.fuel-core-installed ] && [ "$(cat fuel/.fuel-core-installed)" = "${FUEL_CORE_SHA256}" ] && [ -f fuel/packages/core/bootstrap.php ]; then
    return 0
  fi
  if ! command -v unzip >/dev/null; then
    apt-get install -y --no-install-recommends unzip curl ca-certificates
  fi
  curl -fsSL -o /tmp/fuel-core.zip "${FUEL_CORE_URL}"
  echo "${FUEL_CORE_SHA256}  /tmp/fuel-core.zip" | sha256sum -c -
  rm -rf fuel/packages/core /tmp/fuel-core-extract
  mkdir -p /tmp/fuel-core-extract fuel/packages
  unzip -q /tmp/fuel-core.zip -d /tmp/fuel-core-extract
  rm -rf fuel/packages/core
  mv /tmp/fuel-core-extract/* fuel/packages/core
  rmdir /tmp/fuel-core-extract || true
  mkdir -p fuel
  echo "${FUEL_CORE_SHA256}" > fuel/.fuel-core-installed
}
install_fuel_core
if php -r "exit(version_compare(PHP_VERSION, \"8.0.0\", \">=\") ? 0 : 1);"; then
  php tests/filter-composer-php83.php
  COMPOSER_JSON=/tmp/composer.php83.json
  MARKER=vendor/.installed-php83
else
  php tests/prepare-composer-dev.php
  COMPOSER_JSON=/tmp/composer.dev.json
  MARKER=vendor/.installed-php72
fi
if [ ! -f "${MARKER}" ]; then
  rm -rf vendor composer.lock
  COMPOSER="${COMPOSER_JSON}" php /tmp/composer.phar update --no-interaction --prefer-dist
  rm -f vendor/.installed-php72 vendor/.installed-php83
  touch "${MARKER}"
fi
php -r "echo PHP_VERSION, PHP_EOL;"
php /usr/local/bin/phpunit --configuration "${PHPUNIT_CONFIG}" --do-not-cache-result "$@"
' _ "${ORDER_EXTRA[@]}"
