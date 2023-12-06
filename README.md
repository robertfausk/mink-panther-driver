# MinkPantherDriver
[![Latest Stable Version](https://poser.pugx.org/robertfausk/mink-panther-driver/v/stable.svg)](https://packagist.org/packages/robertfausk/mink-panther-driver)
[![Latest Unstable Version](https://poser.pugx.org/robertfausk/mink-panther-driver/v/unstable.svg)](https://packagist.org/packages/robertfausk/mink-panther-driver)
[![Total Downloads](https://poser.pugx.org/robertfausk/mink-panther-driver/downloads.svg)](https://packagist.org/packages/robertfausk/mink-panther-driver)
[![Monhtly Downloads](https://img.shields.io/packagist/dm/robertfausk/mink-panther-driver?style=flat&color=blue)](https://img.shields.io/packagist/dm/robertfausk/mink-panther-driver)
[![Daily Downloads](https://img.shields.io/packagist/dd/robertfausk/mink-panther-driver?style=flat&color=blue)](https://img.shields.io/packagist/dm/robertfausk/mink-panther-driver)
[![Tests](https://github.com/robertfausk/mink-panther-driver/actions/workflows/ci.yml/badge.svg)](https://github.com/robertfausk/mink-panther-driver/actions/workflows/ci.yml)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/robertfausk/mink-panther-driver/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/robertfausk/mink-panther-driver/)
[![Code Coverage](https://scrutinizer-ci.com/g/robertfausk/mink-panther-driver/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/robertfausk/mink-panther-driver/)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat)](LICENSE)
[![PHP Version Require](http://poser.pugx.org/robertfausk/mink-panther-driver/require/php)](https://packagist.org/packages/robertfausk/mink-panther-driver)
[![Open Issues](https://img.shields.io/github/issues-raw/robertfausk/mink-panther-driver?style=flat)](https://github.com/robertfausk/mink-panther-driver/issues)
[![Closed Issues](https://img.shields.io/github/issues-closed-raw/robertfausk/mink-panther-driver?style=flat)](https://github.com/robertfausk/mink-panther-driver/issues?q=is%3Aissue+is%3Aclosed)
[![Contributors](https://img.shields.io/github/contributors/robertfausk/mink-panther-driver?style=flat)](https://github.com/robertfausk/mink-panther-driver/graphs/contributors)
![Contributors](https://img.shields.io/maintenance/yes/2024?style=flat)
[![Dependents](http://poser.pugx.org/robertfausk/mink-panther-driver/dependents)](https://packagist.org/packages/robertfausk/mink-panther-driver/dependents)


Symfony Panther driver for Mink framework

## Install

    composer require --dev robertfausk/mink-panther-driver

Usage Example
-------------

```PHP
<?php

use Behat\Mink\Mink;
use Behat\Mink\Session;
use Behat\Mink\Driver\PantherDriver;
use Symfony\Component\Panther\PantherTestCase;

// These are the same options passed as in PantherTestCaseTrait::createPantherClient client constructor. 
protected static $defaultOptions = [
    'webServerDir' => __DIR__.'/../../../../public', // the Flex directory structure
    'hostname' => '127.0.0.1',
    'port' => 9080,
    'router' => '',
    'external_base_uri' => null,
    'readinessPath' => '',
    'browser' => PantherTestCase::CHROME,
];
$kernelOptions = []; # unused cause we do not extend class KernelTestCase
$managerOptions = [];

$mink = new Mink(array(
    'panther' => new Session(new PantherDriver($defaultOptions, $kernelOptions, $managerOptions)),
));

$mink->getSession('panther')->getPage()->findLink('Chat')->click();
```

Please refer to [MinkExtension-example](https://github.com/Behat/MinkExtension-example) for an executable example.

## Documentation

Since MinkPantherDriver is just glue between Mink and Symfony Panther, it already has an extensive documentation:

* For `Mink` , read [Mink's documentation](http://mink.behat.org/en/latest/).
* For `symfony/panther`, read [Panther's documentation](https://github.com/symfony/panther).
* For usage with `Behat`, read [Behat's documentation](http://behat.org/en/latest/guides.html)
and give [robertfausk/behat-panther-extension](https://github.com/robertfausk/behat-panther-extension) a try.

### How to upgrade?

 Have a look at [CHANGELOG](CHANGELOG.md) for detailed information.

## How to contribute?

Start docker-compose with php web driver by using one of the prepared docker setups with php7.2, php7.3, php7.4, php8.0, php8.1 or php8.2.

    docker-compose up php8.2

Run phpunit tests

    docker-compose exec php8.2 vendor/bin/phpunit

If you run into issues of type ```session not created: This version of ChromeDriver only supports Chrome version 79```
then it is because of mismatched versions between installed chrome driver and chromium.
Best way to bypass this problem is to update them to their newest version.

    docker-compose exec php8.2 vendor/bin/bdi detect drivers

## Credits

Created by Robert Freigang [robertfausk](https://github.com/robertfausk).
 
MinkPantherDriver is built on top of [Panther](https://github.com/symfony/panther) and for usage with [Behat and Mink](http://behat.org/en/latest/cookbooks/integrating_symfony2_with_behat.html#initialising-behat). 
It has been inspired by [MinkBrowserKitDriver](https://github.com/minkphp/MinkBrowserKitDriver) and [MinkSelenium2Driver](https://github.com/minkphp/MinkSelenium2Driver).
