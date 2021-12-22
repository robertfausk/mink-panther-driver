<?php
declare(strict_types=1);

namespace Behat\Mink\Tests\Driver;

use Behat\Mink\Driver\PantherDriver;

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

        return new PantherDriver();
    }

    /**
     * {@inheritdoc}
     */
    public function getWebFixturesUrl()
    {
        return 'http://localhost:8002';
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
