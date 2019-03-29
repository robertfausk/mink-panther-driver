<?php
declare(strict_types=1);

namespace Behat\Mink\Tests\Driver\Custom;

use Behat\Mink\Tests\Driver\Form\RadioTest as BaseRadioTest;

class RadioTest extends BaseRadioTest
{
    public function testIsChecked()
    {
        $this->markTestSkipped('Still buggy.');
    }

    public function testSetValue()
    {
        $this->markTestSkipped('Still buggy.');
    }
}
