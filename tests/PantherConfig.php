<?php
declare(strict_types=1);

namespace Behat\Mink\Tests\Driver;

use Behat\Mink\Driver\PantherDriver;
use Behat\Mink\Tests\Driver\Basic\BasicAuthTest;
use Behat\Mink\Tests\Driver\Basic\HeaderTest;
use Behat\Mink\Tests\Driver\Basic\StatusCodeTest;
use Behat\Mink\Tests\Driver\Js\EventsTest;

class PantherConfig extends AbstractConfig
{

    public static function getInstance()
    {
        return new self();
    }

    /**
     * {@inheritdoc}
     */
    public function createDriver()
    {
        $_SERVER['PANTHER_WEB_SERVER_DIR'] = __DIR__.'/../vendor/mink/driver-testsuite/web-fixtures/';
        $_SERVER['PANTHER_DEVTOOLS'] = false;

        return new PantherDriver();
    }

    /**
     * {@inheritdoc}
     */
    public function getWebFixturesUrl()
    {
        return 'http://localhost:8002';
    }

    public function skipMessage($testCase, $test): ?string
    {
        $testCallback = [$testCase, $test];

        if ([EventsTest::class, 'testKeyboardEvents'] === $testCallback) {
            return <<<TEXT
minkphp/driver-testsuite has a bug in this as far as could be seen.

See https://github.com/minkphp/driver-testsuite/issues/36
TEXT;
        }
        if (BasicAuthTest::class === $testCase) {
            return 'Basic auth is not supported.';
        }
        if (HeaderTest::class === $testCase) {
            return 'Headers are not supported.';
        }
        if (StatusCodeTest::class === $testCase) {
            return 'Checking status code is not supported.';
        }

        return parent::skipMessage($testCase, $test);
    }

    /**
     * {@inheritdoc}
     */
    protected function supportsCss()
    {
        return true;
    }

    protected function supportsJs()
    {
        return true;
    }
}
