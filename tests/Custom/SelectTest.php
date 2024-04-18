<?php
declare(strict_types=1);

namespace Behat\Mink\Tests\Driver\Custom;

use Behat\Mink\Exception\DriverException;
use Behat\Mink\Tests\Driver\Form\SelectTest as BaseSelectTest;
use Behat\Mink\Tests\Driver\TestCase;

/**
 * @see: BaseSelectTest
 */
class SelectTest extends TestCase
{
//    public function testMultiselect(): void
//    {
//        $this->getSession()->visit($this->pathTo('/multiselect_form.html'));
//        $webAssert = $this->getAssertSession();
//        $page = $this->getSession()->getPage();
//        $this->assertEquals('Multiselect Test', $webAssert->elementExists('css', 'h1')->getText());
//
//        $selectWithoutOption = $webAssert->fieldExists('select_without_option');
//        $this->assertSame('', $selectWithoutOption->getValue());
//
//        $selectWithNoOptionSelected = $webAssert->fieldExists('select_first_option_is_selected_by_default');
//        $this->assertEquals('1', $selectWithNoOptionSelected->getValue());
//
//        $select = $webAssert->fieldExists('select_number');
//        $multiSelect = $webAssert->fieldExists('select_multiple_numbers[]');
//        $secondMultiSelect = $webAssert->fieldExists('select_multiple_values[]');
//
//        $this->assertEquals('20', $select->getValue());
//        $this->assertSame([], $multiSelect->getValue());
//        $this->assertSame(['2', '3'], $secondMultiSelect->getValue());
//
//        $select->selectOption('thirty');
//        $this->assertEquals('30', $select->getValue());
//
//        $multiSelect->selectOption('one', true);
//
//        $this->assertSame(['1'], $multiSelect->getValue());
//
//        $multiSelect->selectOption('three', true);
//
//        $this->assertEquals(['1', '3'], $multiSelect->getValue());
//
//        $secondMultiSelect->selectOption('two');
//        $this->assertSame(['2'], $secondMultiSelect->getValue());
//
//        $button = $page->findButton('Register');
//        $this->assertNotNull($button);
//        $button->press();
//
//        $out = <<<'OUT'
//  agreement = `off`,
//  select_first_option_is_selected_by_default = `1`,
//  select_multiple_numbers = array(
//    0 = `1`,
//    1 = `3`,
//  ),
//  select_multiple_values = array(
//    0 = `2`,
//  ),
//  select_number = `30`,
//OUT;
//        $this->assertStringContainsString($out, $page->getContent());
//    }

    /**
     * @dataProvider elementSelectedStateCheckDataProvider
     */
    public function testElementSelectedStateCheck(string $selectName, string $optionValue, string $optionText): void
    {
        $session = $this->getSession();
        $webAssert = $this->getAssertSession();
        $session->visit($this->pathTo('/multiselect_form.html'));
        $select = $webAssert->fieldExists($selectName);

        $option = $webAssert->elementExists('named', ['option', $optionValue], $select);

        $this->assertFalse($option->isSelected());
        $select->selectOption($optionText);
        $this->assertTrue($option->isSelected());
    }

    public static function elementSelectedStateCheckDataProvider(): iterable
    {
        return [
            ['select_number', '30', 'thirty'],
            ['select_multiple_numbers[]', '2', 'two'],
        ];
    }

    public function testSetValueSingleSelect(): void
    {
        $session = $this->getSession();
        $session->visit($this->pathTo('/multiselect_form.html'));
        $select = $this->getAssertSession()->fieldExists('select_number');

        $select->setValue('10');
        $this->assertEquals('10', $select->getValue());
    }

//    public function testSetValueMultiSelect(): void
//    {
//        $session = $this->getSession();
//        $session->visit($this->pathTo('/multiselect_form.html'));
//        $select = $this->getAssertSession()->fieldExists('select_multiple_values[]');
//
//        $select->setValue(['1', '2']);
//        $this->assertEquals(['1', '2'], $select->getValue());
//    }

    /**
     * @dataProvider provideBooleanValues
     */
    public function testSetBooleanValue(bool $value): void
    {
        $session = $this->getSession();
        $session->visit($this->pathTo('/multiselect_form.html'));
        $select = $this->getAssertSession()->fieldExists('select_number');

        $this->expectException(DriverException::class);
        $select->setValue($value);
    }

    public static function provideBooleanValues(): iterable
    {
        yield [true];
        yield [false];
    }

    /**
     * @see https://github.com/Behat/Mink/issues/193
     */
//    public function testOptionWithoutValue(): void
//    {
//        $session = $this->getSession();
//        $session->visit($this->pathTo('/issue193.html'));
//
//        $session->getPage()->selectFieldOption('options-without-values', 'Two');
//        $this->assertEquals('Two', $this->findById('options-without-values')->getValue());
//
//        $this->assertTrue($this->findById('two')->isSelected());
//        $this->assertFalse($this->findById('one')->isSelected());
//
//        $session->getPage()->selectFieldOption('options-with-values', 'two');
//        $this->assertEquals('two', $this->findById('options-with-values')->getValue());
//    }

    /**
     * @see https://github.com/Behat/Mink/issues/131
     */
    public function testAccentuatedOption(): void
    {
        $this->getSession()->visit($this->pathTo('/issue131.html'));
        $page = $this->getSession()->getPage();

        $page->selectFieldOption('foobar', 'Gimme some accentués characters');

        $field = $page->findField('foobar');

        $this->assertNotNull($field);
        $this->assertEquals('1', $field->getValue());
    }

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
