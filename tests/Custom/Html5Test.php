<?php
declare(strict_types=1);

namespace Behat\Mink\Tests\Driver\Custom;

use Behat\Mink\Exception\DriverException;
use Behat\Mink\Tests\Driver\Form\Html5Test as BaseHtml5Test;
use Behat\Mink\Tests\Driver\TestCase;

/**
 * @see BaseHtml5Test
 */
class Html5Test extends TestCase
{

    public function testHtml5FormInputAttribute(): void
    {
        $this->getSession()->visit($this->pathTo('/html5_form.html'));
        $page = $this->getSession()->getPage();
        $webAssert = $this->getAssertSession();

        $firstName = $webAssert->fieldExists('first_name');
        $lastName = $webAssert->fieldExists('last_name');

        $this->assertEquals('not set', $lastName->getValue());
        $firstName->setValue('John');
        $lastName->setValue('Doe');

        $this->assertEquals('Doe', $lastName->getValue());

        $page->pressButton('Submit in form');

        if ($this->safePageWait(5000, 'document.getElementsByTagName("title") !== null')) {
            $out = <<<'OUT'
  first_name = `John`,
  last_name = `Doe`,
OUT;
            $this->assertStringContainsString($out, $page->getContent());
            $this->assertStringNotContainsString('other_field', $page->getContent());
        }
    }

//    public function testHtml5FormRadioAttribute(): void
//    {
//        $this->getSession()->visit($this->pathTo('html5_radio.html'));
//        $page = $this->getSession()->getPage();
//
//        $radio = $this->findById('sex_f');
//        $otherRadio = $this->findById('sex_invalid');
//
//        $this->assertEquals('f', $radio->getValue());
//        $this->assertEquals('invalid', $otherRadio->getValue());
//
//        $radio->selectOption('m');
//
//        $this->assertEquals('m', $radio->getValue());
//        $this->assertEquals('invalid', $otherRadio->getValue());
//
//        $page->pressButton('Submit in form');
//
//        $out = <<<'OUT'
//  sex = `m`,
//OUT;
//        $this->assertStringContainsString($out, $page->getContent());
//    }

    public function testHtml5FormButtonAttribute(): void
    {
        $this->getSession()->visit($this->pathTo('/html5_form.html'));
        $page = $this->getSession()->getPage();
        $webAssert = $this->getAssertSession();

        $firstName = $webAssert->fieldExists('first_name');
        $lastName = $webAssert->fieldExists('last_name');

        $firstName->setValue('John');
        $lastName->setValue('Doe');

        $page->pressButton('Submit outside form');

        if ($this->safePageWait(5000, 'document.getElementsByTagName("title") !== null')) {
            $out = <<<'OUT'
  first_name = `John`,
  last_name = `Doe`,
  submit_button = `test`,
OUT;
            $this->assertStringContainsString($out, $page->getContent());
        }
    }

    public function testHtml5FormOutside(): void
    {
        $this->getSession()->visit($this->pathTo('/html5_form.html'));
        $page = $this->getSession()->getPage();

        $page->fillField('other_field', 'hello');

        $page->pressButton('Submit separate form');

        if ($this->safePageWait(5000, 'document.getElementsByTagName("title") !== null')) {
            $out = <<<'OUT'
  other_field = `hello`,
OUT;
            $this->assertStringContainsString($out, $page->getContent());
            $this->assertStringNotContainsString('first_name', $page->getContent());
        }
    }

    public function testHtml5Types(): void
    {
        $this->getSession()->visit($this->pathTo('html5_types.html'));
        $page = $this->getSession()->getPage();

        $page->fillField('url', 'https://mink.behat.org/');
        $page->fillField('email', 'mink@example.org');
        $page->fillField('number', '6');
        $page->fillField('search', 'mink');
        $page->fillField('date', '1111-11-11');
        $page->fillField('time', '14:12');
        $page->fillField('color', '#ff00aa');

        $page->pressButton('Submit');

        $out = <<<'OUT'
  color = `#ff00aa`,
  date = `1111-11-11`,
  email = `mink@example.org`,
  number = `6`,
  search = `mink`,
  submit_button = `Submit`,
  time = `14:12`,
  url = `https://mink.behat.org/`,
OUT;

        $this->assertStringContainsString($out, $page->getContent());
    }

    public function testHtml5FormAction(): void
    {
        $this->getSession()->visit($this->pathTo('html5_form.html'));
        $page = $this->getSession()->getPage();

        $page->fillField('first_name', 'Jimmy');
        $page->pressButton('Submit to basic form');

        if ($this->safePageWait(5000, 'document.getElementsByTagName("title") !== null')) {
            $this->assertStringContainsString('<title>Basic Form Saving</title>', $page->getContent());
            $this->assertStringContainsString('Firstname: Jimmy', $page->getContent());
        }
    }

    public function testHtml5FormMethod(): void
    {
        $this->getSession()->visit($this->pathTo('html5_form.html'));
        $page = $this->getSession()->getPage();

        $page->fillField('first_name', 'Jimmy');
        $page->fillField('last_name', 'Jones');
        $page->pressButton('Submit as GET');

        if ($this->safePageWait(5000, 'document.getElementsByTagName("title") !== null')) {
            $this->assertEquals(
                $this->pathTo('advanced_form_post.php') . '?first_name=Jimmy&last_name=Jones',
                $this->getSession()->getCurrentUrl()
            );
        }
    }

    /**
     * @dataProvider provideInvalidValues
     *
     * @param mixed $value
     */
    public function testSetInvalidValueInField(string $field, $value): void
    {
        $this->getSession()->visit($this->pathTo('/html5_types.html'));

        $webAssert = $this->getAssertSession();

        $color = $webAssert->fieldExists($field);

        $this->expectException(DriverException::class);
        $color->setValue($value);
    }

    public static function provideInvalidValues(): iterable
    {
        $trueValue = ['true', true];
        $falseValue = ['false', false];
        $arrayValue = ['empty array', []];

        $scenarios = [
            // field type, name or id, list of values to check
            ['url', 'url', [$trueValue, $falseValue, $arrayValue]],
            ['email', 'email', [$trueValue, $falseValue, $arrayValue]],
            ['number', 'number', [$trueValue, $falseValue, $arrayValue]],
            ['search', 'search', [$trueValue, $falseValue, $arrayValue]],
            ['color', 'color', [$trueValue, $falseValue, $arrayValue]],
            ['date', 'date', [$trueValue, $falseValue, $arrayValue]],
            ['time', 'time', [$trueValue, $falseValue, $arrayValue]],
        ];

        foreach ($scenarios as [$fieldType, $fieldNameOrId, $values]) {
            foreach ($values as [$valueDesc, $actualValue]) {
                yield "$fieldType field with $valueDesc" => [$fieldNameOrId, $actualValue];
            }
        }
    }

    public function testHtml5FormRadioAttribute()
    {
        $this->markTestSkipped('Still buggy.');
        parent::testHtml5FormRadioAttribute();
    }
}
