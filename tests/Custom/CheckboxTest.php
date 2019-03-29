<?php
declare(strict_types=1);

namespace Behat\Mink\Tests\Driver\Custom;

use Behat\Mink\Tests\Driver\Form\CheckboxTest as BaseCheckboxTest;

class CheckboxTest extends BaseCheckboxTest
{
    public function testCheckboxMultiple()
    {
        $this->markTestSkipped('Still buggy.');
        parent::testCheckboxMultiple();
    }
}
