<?php
declare(strict_types=1);

namespace Behat\Mink\Tests\Driver;

use Behat\Mink\Exception\DriverException;

class GetContentTest extends TestCase
{
    public function test_throws_an_exception_if_driver_not_startet():void
    {
        $this->expectException(DriverException::class);
        $this->expectExceptionMessage('Client is not (yet) started.');
        $driver = $this->getSession()->getDriver();
        if ($driver->isStarted()) {
            $driver->stop();
        }
        $driver->getContent();
    }
}
