1.0.3 / 2020-03-28
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


1.0.2 / 2020-03-28
==================

Restricted [```symfony/panther```](https://github.com/symfony/panther) version to ```~0.4,<0.7```
So if you want to use old panther version then use this version
This library will drop support for ```symfony/panther``` with versions ```<0.7```

Misc:
 
* Added hint about issue ```This version of ChromeDriver only supports Chrome version``` to [README](README.md)


1.0.1 / 2019-10-11
==================

Misc:

* Used official [```symfony/panther```](https://github.com/symfony/panther) repository instead of own repository [```robertfausk/panther```](https://github.com/robertfausk/panther) 
* Added more badges to [README](README.md) (SensioLabsInsight, Software License, supported PHP versions, Open/Closed Issuse and Contributors)


1.0.0 / 2019-08-16
==================

Initial Release :tada: 
