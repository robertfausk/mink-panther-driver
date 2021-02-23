#!/usr/bin/env bash

##############################################################
# execute the default command
##############################################################
composer install

vendor/bin/mink-test-server
