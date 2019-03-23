<?php
declare(strict_types=1);

namespace Behat\Mink\Tests\Driver\Custom;

use Behat\Mink\Driver\PantherDriver;
use Behat\Mink\Exception\DriverException;
use PHPUnit\Framework\TestCase;

class ErrorHandlingTest extends TestCase
{
    /**
     * Looks like we have to mark these tests as "legacy", otherwise we get deprecation errors.
     * Although the deprecations are handled, there's no way to avoid the deprecation message here.
     *
     * @group legacy
     */
    public function testFindWithoutVisit()
    {
        $this->expectException(DriverException::class);
        $this->expectExceptionMessage('Unable to access the response content before visiting a page');

        $this->getDriver()->find('//html');
    }

    /**
     * Looks like we have to mark these tests as "legacy", otherwise we get deprecation errors.
     * Although the deprecations are handled, there's no way to avoid the deprecation message here.
     *
     * @group legacy
     */
    public function testGetCurrentUrlWithoutVisit()
    {
        $this->expectException(DriverException::class);
        $this->expectExceptionMessage('Unable to access the request before visiting a page');

        $this->getDriver()->getCurrentUrl();
    }

    private function getDriver()
    {
        return new PantherDriver();
    }
}
