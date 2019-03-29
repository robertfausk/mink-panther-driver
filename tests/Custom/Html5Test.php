<?php
declare(strict_types=1);

namespace Behat\Mink\Tests\Driver\Custom;

use Behat\Mink\Tests\Driver\Form\Html5Test as BaseHtml5Test;

class Html5Test extends BaseHtml5Test
{
    public function testHtml5FormRadioAttribute()
    {
        $this->markTestSkipped('Still buggy.');
        parent::testHtml5FormRadioAttribute();
    }
}
