<?php
declare(strict_types=1);

namespace Behat\Mink\Tests\Driver\Custom;

use Behat\Mink\Tests\Driver\Js\EventsTest as BaseEventsTest;

class EventsTest extends BaseEventsTest
{
    public function provideKeyboardEventsModifiers()
    {
        return array(
            'none' => array(null, '0 / 0 / 0 / 0'),
            // @see: PantherDriver::reset seems to not release all old keys like alt, control etc.
            // 'alt' => array('alt', '1 / 0 / 0 / 0'),
            // jQuery considers ctrl as being a metaKey in the normalized event
             'ctrl' => array('ctrl', '0 / 1 / 0 / 1'),
            // 'shift' => array('shift', '0 / 0 / 1 / 0'),// @see: https://github.com/minkphp/driver-testsuite/issues/36
            // 'meta' => array('meta', '0 / 0 / 0 / 1'),
        );
    }

}
