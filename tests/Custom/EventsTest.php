<?php
declare(strict_types=1);

namespace Behat\Mink\Tests\Driver\Custom;

use Behat\Mink\Tests\Driver\Js\EventsTest as BaseEventsTest;
use Behat\Mink\Tests\Driver\TestCase;

/**
 * @see BaseEventsTest
 */
class EventsTest extends TestCase
{
    /**
     * @group mouse-events
     */
    public function testClick(): void
    {
        $this->getSession()->visit($this->pathTo('/js_test.html'));
        $clicker = $this->getAssertSession()->elementExists('css', '.elements div#clicker');
        $this->assertEquals('not clicked', $clicker->getText());

        $clicker->click();
        $this->assertEquals('single clicked', $clicker->getText());
    }

    /**
     * @group mouse-events
     */
    public function testDoubleClick(): void
    {
        $this->getSession()->visit($this->pathTo('/js_test.html'));
        $clicker = $this->getAssertSession()->elementExists('css', '.elements div#clicker');
        $this->assertEquals('not clicked', $clicker->getText());

        $clicker->doubleClick();
        $this->assertEquals('double clicked', $clicker->getText());
    }

    /**
     * @group mouse-events
     */
    public function testRightClick(): void
    {
        $this->getSession()->visit($this->pathTo('/js_test.html'));
        $clicker = $this->getAssertSession()->elementExists('css', '.elements div#clicker');
        $this->assertEquals('not clicked', $clicker->getText());

        $clicker->rightClick();
        $this->assertEquals('right clicked', $clicker->getText());
    }

    /**
     * @group mouse-events
     */
    public function testFocus(): void
    {
        $this->getSession()->visit($this->pathTo('/js_test.html'));
        $focusBlurDetector = $this->getAssertSession()->elementExists('css', '.elements input#focus-blur-detector');
        $this->assertEquals('no action detected', $focusBlurDetector->getValue());

        $focusBlurDetector->focus();
        $this->assertEquals('focused', $focusBlurDetector->getValue());
    }

    /**
     * @group mouse-events
     * @depends testFocus
     */
    public function testBlur(): void
    {
        $this->getSession()->visit($this->pathTo('/js_test.html'));
        $focusBlurDetector = $this->getAssertSession()->elementExists('css', '.elements input#focus-blur-detector');
        $this->assertEquals('no action detected', $focusBlurDetector->getValue());

        $focusBlurDetector->focus();
        $focusBlurDetector->blur();
        $this->assertEquals('blured', $focusBlurDetector->getValue());
    }

    /**
     * @group mouse-events
     */
    public function testMouseOver(): void
    {
        $this->getSession()->visit($this->pathTo('/js_test.html'));
        $mouseOverDetector = $this->getAssertSession()->elementExists('css', '.elements div#mouseover-detector');
        $this->assertEquals('no mouse action detected', $mouseOverDetector->getText());

        $mouseOverDetector->mouseOver();
        $this->assertEquals('mouse overed', $mouseOverDetector->getText());
    }

//    /**
//     * @param KeyModifier::*|null $modifier
//     *
//     * @dataProvider provideKeyboardEventsModifiers
//     */
//    public function testKeyboardEvents(?string $modifier, string $eventProperties): void
//    {
//        $this->getSession()->visit($this->pathTo('/js_test.html'));
//        $webAssert = $this->getAssertSession();
//
//        $input1 = $webAssert->elementExists('css', '.elements input.input.first');
//        $input2 = $webAssert->elementExists('css', '.elements input.input.second');
//        $input3 = $webAssert->elementExists('css', '.elements input.input.third');
//        $event = $webAssert->elementExists('css', '.elements .text-event');
//
//        $input1->keyDown('u', $modifier);
//        $this->assertEquals('key downed:' . $eventProperties, $event->getText());
//
//        $input2->keyPress('r', $modifier);
//        $this->assertEquals('key pressed:114 / ' . $eventProperties, $event->getText());
//
//        $input2->keyPress('R', $modifier);
//        $this->assertEquals('key pressed:82 / ' . $eventProperties, $event->getText());
//
//        $input2->keyPress('0', $modifier);
//        $this->assertEquals('key pressed:48 / ' . $eventProperties, $event->getText());
//
//        $input3->keyUp(78, $modifier);
//        $this->assertEquals('key upped:78 / ' . $eventProperties, $event->getText());
//    }
//
//    public static function provideKeyboardEventsModifiers(): iterable
//    {
//        return [
//            'none' => [null, '0 / 0 / 0 / 0'],
//            'alt' => [KeyModifier::ALT, '1 / 0 / 0 / 0'],
//            // jQuery considers ctrl as being a metaKey in the normalized event
//            'ctrl' => [KeyModifier::CTRL, '0 / 1 / 0 / 1'],
//            'shift' => [KeyModifier::SHIFT, '0 / 0 / 1 / 0'],
//            'meta' => [KeyModifier::META, '0 / 0 / 0 / 1'],
//        ];
//    }

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
