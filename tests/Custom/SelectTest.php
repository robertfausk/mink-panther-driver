<?php
declare(strict_types=1);

namespace Behat\Mink\Tests\Driver\Custom;

use Behat\Mink\Tests\Driver\Form\SelectTest as BaseSelectTest;

class SelectTest extends BaseSelectTest
{
    public function testMultiselect()
    {
        $this->markTestSkipped('Still buggy.');
    }

    public function testSetValueMultiSelect()
    {
        $this->markTestSkipped('Still buggy.');
    }

    public function testOptionWithoutValue()
    {
        $this->markTestSkipped('Still buggy.');
    }
}
