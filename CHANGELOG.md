vx.x.x / 2024-xx-xx
===================

v1.1.1 / 2024-04-18
===================

Features:
* Add support for ```PHP 8.2``` #28
* Adopt code to changes in mink/driver-testsuite #29
* Add support for ```PHP 8.3``` #30

Misc:

* Rename branch ```master``` to ```main``` cause black lives matter. #26 #27

v1.1.0 / 2022-07-25
===================

Features:
* Add support for ```symfony/panther:~2.0``` and ```symfony``` ```~6.0```.
* Add support for ```PHP 8.1```.
* Drop support for ```PHP 7.1``` cause of too much maintenance afford. Stick with ```v1.0.8``` if you want to use ```PHP 7.1```.
* Enable RadioTest::testIsChecked #11 and RadioTest::testSetValue #10 as soon as ```symfony/panther#v2.0.2``` ist out. 
See related PR: https://github.com/symfony/panther/pull/526

Misc:
* Use GitHub Actions instead of Travis CI for continuous integration. #22

v1.0.8 / 2021-03-14
==================

Features:

* Allow to pass kernel options and manager options to panther driver (#19 and #20; Thx to @phcorp)

v1.0.7 / 2021-02-23
==================

Features:

* Add support for ```symfony/panther:~1.0``` (Thx to @Haehnchen)
* Add support for ```PHP 8.0```

Misc:

* Use ```dbrekelmans/bdi``` for fetching chromedriver if not on PHP 7.1.
  Keep using ```bin/updatePantherChromeDriver.sh``` if working with PHP 7.1.
* Support and use composer 2 in Dockerfile
* Add support for local development with ```PHP 7.1```, ```PHP 7.2```, ```PHP 7.3```, ```PHP 7.4``` and ```PHP 8.0``` via ```docker-compose.yml```

v1.0.6 / 2020-08-08
==================

Features:

* Add support for ```symfony/panther:0.8```

Fixes:

* Catch LogicException when using not implemented getHistory introduced in symfony/panther 0.8
* Throw exception when using getClient and driver is not started.
  This ensures compatibility with https://github.com/Behatch/contexts
  see: https://github.com/Behatch/contexts/issues/284
  
v1.0.5 / 2020-06-26
==================

Fixes:

* Fix localStorage access not allowed for reset() on certain pages ('', 'about:blank', 'data:,') #17 (Thx to @Lctrs)

v1.0.4 / 2020-04-14
==================

Fixes:

* Clear localStorage on reset()

v1.0.3 / 2020-03-28
==================

Features:

* Bumped ```symfony/panther``` to ```~0.7```
* Bumped ```behat/mink``` to ```~1.8```
* Added support for ```keyUp```, ```keyDown``` and ```sendKeys```
* Added support for html5 input type ```type```

Testsuite:

* Added PHP 7.4 in the CI
* Removed allowed failures for PHP 7.4

Fixes:

* Added constants required by PantherTestCase (Thx to @Haehnchen & @symfonyaml)
* Fixed ```switchToIFrame``` when a string is provided

Misc:
 
* Added hint about issue ```This version of ChromeDriver only supports Chrome version``` to [README](README.md)


v1.0.2 / 2020-03-28
==================

Restricted [```symfony/panther```](https://github.com/symfony/panther) version to ```~0.4,<0.7```
So if you want to use old panther version then use this version
This library will drop support for ```symfony/panther``` with versions ```<0.7```

Misc:
 
* Added hint about issue ```This version of ChromeDriver only supports Chrome version``` to [README](README.md)


v1.0.1 / 2019-10-11
==================

Misc:

* Used official [```symfony/panther```](https://github.com/symfony/panther) repository instead of own repository [```robertfausk/panther```](https://github.com/robertfausk/panther) 
* Added more badges to [README](README.md) (SensioLabsInsight, Software License, supported PHP versions, Open/Closed Issuse and Contributors)


v1.0.0 / 2019-08-16
==================

Initial Release :tada: 
