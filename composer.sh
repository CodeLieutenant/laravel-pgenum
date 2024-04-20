#!/bin/sh

docker run --rm \
  -u "$(id -u):$(id -g)" \
  -v "$(pwd):/var/www/html" \
  -w /var/www/html \
  -e COMPOSER_ALLOW_SUPERUSER=1 \
  laravelsail/php81-composer:latest \
  composer $@