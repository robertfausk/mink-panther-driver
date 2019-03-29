<?php
declare(strict_types=1);

namespace Behat\Mink\Tests\Driver\Custom;

use Behat\Mink\Tests\Driver\Form\GeneralTest as BaseGeneralTest;

class GeneralTest extends BaseGeneralTest
{
    public function testAdvancedForm()
    {
        $this->getSession()->visit($this->pathTo('/advanced_form.html'));
        $page = $this->getSession()->getPage();

        $page->fillField('first_name', 'ever');
        $page->fillField('last_name', 'zet');

        $page->pressButton('Register');

        $this->assertContains('no file', $page->getContent());

        $this->getSession()->visit($this->pathTo('/advanced_form.html'));

        $webAssert = $this->getAssertSession();
        $page = $this->getSession()->getPage();
        $this->assertEquals('ADvanced Form Page', $webAssert->elementExists('css', 'h1')->getText());

        $firstname = $webAssert->fieldExists('first_name');
        $lastname = $webAssert->fieldExists('lastn');
        $email = $webAssert->fieldExists('Your email:');
        $select = $webAssert->fieldExists('select_number');
        $sex = $webAssert->fieldExists('sex');
        $maillist = $webAssert->fieldExists('mail_list');
        $agreement = $webAssert->fieldExists('agreement');
        $notes = $webAssert->fieldExists('notes');
        $about = $webAssert->fieldExists('about');

        $this->assertEquals('Firstname', $firstname->getValue());
        $this->assertEquals('Lastname', $lastname->getValue());
        $this->assertEquals('your@email.com', $email->getValue());
        $this->assertEquals('20', $select->getValue());
        $this->assertEquals('w', $sex->getValue());
        $this->assertEquals('original notes', $notes->getValue());

        $this->assertEquals('on', $maillist->getValue());
        $this->assertNull($agreement->getValue());

        $this->assertTrue($maillist->isChecked());
        $this->assertFalse($agreement->isChecked());

        $agreement->check();
        $this->assertTrue($agreement->isChecked());

        $maillist->uncheck();
        $this->assertFalse($maillist->isChecked());

        $select->selectOption('thirty');
        $this->assertEquals('30', $select->getValue());

        $sex->selectOption('m');
        $this->assertEquals('m', $sex->getValue());

        $notes->setValue('new notes');
        $this->assertEquals('new notes', $notes->getValue());

        $about->attachFile($this->mapRemoteFilePath(__DIR__.'/../../web-fixtures/some_file.txt'));

        $button = $page->findButton('Register');
        $this->assertNotNull($button);

        $page->fillField('first_name', 'Foo item');
        $page->fillField('last_name', 'Bar');
        $page->fillField('Your email:', 'ever.zet@gmail.com');

        $this->assertEquals('Foo item', $firstname->getValue());
        $this->assertEquals('Bar', $lastname->getValue());

        $button->press();

        // only change is here; see PR: https://github.com/minkphp/driver-testsuite/pull/35
        if ($this->safePageWait(5000, 'document.getElementsByTagName("title") === "Advanced form save"')) {
            $out = <<<'OUT'
array(
  agreement = `on`,
  email = `ever.zet@gmail.com`,
  first_name = `Foo item`,
  last_name = `Bar`,
  notes = `new notes`,
  select_number = `30`,
  sex = `m`,
  submit = `Register`,
)
some_file.txt
1 uploaded file
OUT;
            $this->assertContains($out, $page->getContent());
        }
    }
}
