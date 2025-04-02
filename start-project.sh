#!/usr/bin/env bash

##############################################################
# execute the default command
##############################################################

git config --global --add safe.directory /var/www/html

if [[ "${PHP_VERSION}" = "8.0*" ]]; then
  echo 'composer install with no php platform requirements'
  composer install --ignore-platform-req=php
el
  echo 'composer install with platform requirements'
  composer install
fi

vendor/bin/bdi detect drivers

vendor/bin/mink-test-server
