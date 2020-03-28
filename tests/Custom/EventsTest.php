<?php
declare(strict_types=1);

namespace Behat\Mink\Tests\Driver\Custom;

use Behat\Mink\Tests\Driver\Js\EventsTest as BaseEventsTest;

class EventsTest extends BaseEventsTest
{
    /**
     * @dataProvider provideKeyboardEventsModifiers
     */
    public function testKeyboardEvents($modifier, $eventProperties)
    {
        // we use custom html file cause when using keyUp then we cannot release key and modifier at once
        // which will break base test
        file_put_contents(
            __DIR__.'/../../vendor/mink/driver-testsuite/web-fixtures/js_test_custom.html',
            file_get_contents(__DIR__.'/web-fixtures/js_test.html')
        );
        $this->getSession()->visit($this->pathTo('/js_test_custom.html'));
        $webAssert = $this->getAssertSession();

        $input1 = $webAssert->elementExists('css', '.elements input.input.first');
        $input2 = $webAssert->elementExists('css', '.elements input.input.second');
        $input3 = $webAssert->elementExists('css', '.elements input.input.third');
        $event = $webAssert->elementExists('css', '.elements .text-event');

        $input1->keyDown('u', $modifier);
        $this->assertStringContainsString('key downed:'.$eventProperties, $event->getText());

        $input2->keyPress('r', $modifier);
        if ('shift' === $modifier) {
            $this->assertStringContainsString('key pressed:82 / '.$eventProperties, $event->getText());
        } else {
            $this->assertStringContainsString('key pressed:114 / '.$eventProperties, $event->getText());
        }

        $input3->keyUp(78, $modifier);
        $this->assertStringContainsString('key upped:78 / '.$eventProperties, $event->getText());
    }

    public function provideKeyboardEventsModifiers()
    {
        return array(
            'none' => array(null, '0 / 0 / 0 / 0'),
            'alt' => array('alt', '1 / 0 / 0 / 0'),
            'ctrl' => array('ctrl', '0 / 1 / 0 / 1'),
            'shift' => array('shift', '0 / 0 / 1 / 0'),
            'meta' => array('meta', '0 / 0 / 0 / 1'),
        );
    }

}
