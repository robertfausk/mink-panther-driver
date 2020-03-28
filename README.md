# MinkPantherDriver
[![Latest Stable Version](https://poser.pugx.org/robertfausk/mink-panther-driver/v/stable.svg)](https://packagist.org/packages/robertfausk/mink-panther-driver)
[![Latest Unstable Version](https://poser.pugx.org/robertfausk/mink-panther-driver/v/unstable.svg)](https://packagist.org/packages/robertfausk/mink-panther-driver)
[![Total Downloads](https://poser.pugx.org/robertfausk/mink-panther-driver/downloads.svg)](https://packagist.org/packages/robertfausk/mink-panther-driver)
[![License](https://poser.pugx.org/robertfausk/mink-panther-driver/license.svg)](https://packagist.org/packages/robertfausk/mink-panther-driver)
[![Build Status](https://travis-ci.org/robertfausk/mink-panther-driver.svg?branch=master)](https://travis-ci.org/robertfausk/MinkPantherDriver)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/robertfausk/mink-panther-driver/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/robertfausk/MinkPantherDriver/)
[![Code Coverage](https://scrutinizer-ci.com/g/robertfausk/mink-panther-driver/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/robertfausk/MinkPantherDriver/)
[![SensioLabsInsight](https://img.shields.io/symfony/i/grade/43846aa7-7ce4-4e33-95f4-115bf9a7e23e?style=flat-square)](https://insight.sensiolabs.com/projects/43846aa7-7ce4-4e33-95f4-115bf9a7e23e)
[![SensioLabsInsight](https://img.shields.io/symfony/i/violations/43846aa7-7ce4-4e33-95f4-115bf9a7e23e?style=flat-square)](https://insight.sensiolabs.com/projects/43846aa7-7ce4-4e33-95f4-115bf9a7e23e)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
![PHP7 Compatible](https://img.shields.io/travis/php-v/robertfausk/mink-panther-driver/master?style=flat-square)
[![Open Issues](https://img.shields.io/github/issues-raw/robertfausk/mink-panther-driver?style=flat-square)](https://github.com/robertfausk/mink-panther-driver/issues)
[![Closed Issues](https://img.shields.io/github/issues-closed-raw/robertfausk/mink-panther-driver?style=flat-square)](https://github.com/robertfausk/mink-panther-driver/issues?q=is%3Aissue+is%3Aclosed)
[![Contributors](https://img.shields.io/github/contributors/robertfausk/mink-panther-driver?style=flat-square)](https://github.com/robertfausk/mink-panther-driver/graphs/contributors)
![Contributors](https://img.shields.io/maintenance/yes/2020?style=flat-square)

Symfony Panther driver for Mink framework

## Install

    composer req --dev behat/mink robertfausk/mink-panther-driver

Usage Example
-------------

```PHP
<?php

use Behat\Mink\Mink,
    Behat\Mink\Session,
    Behat\Mink\Driver\PantherDriver;

protected static $defaultOptions = [
     'webServerDir' => __DIR__.'/../../../../public', // the Flex directory structure
     'hostname' => '127.0.0.1',
     'port' => 9080,
     'router' => '',
     'external_base_uri' => null,
 ];

$mink = new Mink(array(
    'panther' => new Session(new PantherDriver('panther', $defaultOptions, [])),
));

$mink->getSession('panther')->getPage()->findLink('Chat')->click();
```

Please refer to [MinkExtension-example](https://github.com/Behat/MinkExtension-example) for an executable example.

## Documentation

Since MinkPantherDriver is just glue between Mink and Symfony Panther, it already has an extensive documentation:

* For `Mink` , read [Mink's documentation](http://mink.behat.org/en/latest/)
* For `symfony/panther`, read [Panther's documentation](https://github.com/symfony/panther)
* For usage with `Behat`, read [Behat's documentation](http://behat.org/en/latest/guides.html)

### How to upgrade?

 Have a look at [CHANGELOG](CHANGELOG.md) for detailed information.

## How to contribute?

Start docker-compose with php web driver

    docker-compose up php7.2

Run phpunit tests

    docker-compose exec php7.2 vendor/bin/phpunit

## Credits

Created by Robert Freigang [robertfausk](https://github.com/robertfausk).

MinkPantherDriver is built on top of [Panther](https://github.com/symfony/panther) and for usage with [Behat and Mink](http://behat.org/en/latest/cookbooks/integrating_symfony2_with_behat.html#initialising-behat). 
It has been inspired by [MinkBrowserKitDriver](https://github.com/minkphp/MinkBrowserKitDriver) and [MinkSelenium2Driver](https://github.com/minkphp/MinkSelenium2Driver).
