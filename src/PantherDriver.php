<?php
declare(strict_types=1);

/*
 * This file is part of the Behat\Mink.
 * (c) Robert Freigang <robert.freigang@gmx.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Behat\Mink\Driver;

use Behat\Mink\Exception\DriverException;
use Behat\Mink\Exception\UnsupportedDriverActionException;
use Facebook\WebDriver\WebDriverElement;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\DomCrawler\Field\FormField;
use Symfony\Component\Panther\Client;
use Symfony\Component\Panther\DomCrawler\Crawler;
use Symfony\Component\Panther\DomCrawler\Field\ChoiceFormField;
use Symfony\Component\Panther\DomCrawler\Field\FileFormField;
use Symfony\Component\Panther\DomCrawler\Field\InputFormField;
use Symfony\Component\Panther\DomCrawler\Field\TextareaFormField;
use Symfony\Component\Panther\DomCrawler\Form;
use Symfony\Component\Panther\DomCrawler\Link;
use Symfony\Component\Panther\PantherTestCaseTrait;

/**
 * Symfony2 Panther driver.
 *
 * @author Robert Freigang <robertfreigang@gmx.de>
 */
class PantherDriver extends CoreDriver
{
    use PantherTestCaseTrait;

    /** @var Client */
    private $client;

    /**
     * @var Form[]
     */
    private $forms = array();
    private $started = false;
    private $removeScriptFromUrl = false;
    private $removeHostFromUrl = false;

    /**
     * Initializes Panther driver.
     * external_base_uri
     * webServerDir PANTHER_WEB_SERVER_DIR
     * port PANTHER_WEB_SERVER_PORT
     * router PANTHER_WEB_SERVER_ROUTER
     *    protected static $defaultOptions = [
     *        'webServerDir' => __DIR__.'/../../../../public', // the Flex directory structure
     *        'hostname' => '127.0.0.1',
     *        'port' => 9080,
     *        'router' => '',
     *        'external_base_uri' => null,
     *    ];
     *
     * @param string   $clientType BrowserKit client instance
     * @param array    $options
     * @param array    $kernelOptions
     */
    public function __construct(
        string $clientType = 'panther',
        array $options = [],
        array $kernelOptions = []
    ) {
        if ($clientType === 'panther') {
            $client = self::createPantherClient($options, $kernelOptions);
        } elseif ($clientType === 'goutte') {
            $client = self::createGoutteClient($options, $kernelOptions);
        } else {
            throw new \InvalidArgumentException('$clientType have to be one of panther or goutte');
        }

        $this->client = $client;
    }

    /**
     * Returns BrowserKit HTTP client instance.
     *
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Tells driver to remove hostname from URL.
     *
     * @param Boolean $remove
     *
     * @deprecated Deprecated as of 1.2, to be removed in 2.0. Pass the base url in the constructor instead.
     */
    public function setRemoveHostFromUrl($remove = true)
    {
        @trigger_error(
            'setRemoveHostFromUrl() is deprecated as of 1.2 and will be removed in 2.0. Pass the base url in the constructor instead.',
            E_USER_DEPRECATED
        );
        $this->removeHostFromUrl = (bool)$remove;
    }

    /**
     * Tells driver to remove script name from URL.
     *
     * @param Boolean $remove
     *
     * @deprecated Deprecated as of 1.2, to be removed in 2.0. Pass the base url in the constructor instead.
     */
    public function setRemoveScriptFromUrl($remove = true)
    {
        @trigger_error(
            'setRemoveScriptFromUrl() is deprecated as of 1.2 and will be removed in 2.0. Pass the base url in the constructor instead.',
            E_USER_DEPRECATED
        );
        $this->removeScriptFromUrl = (bool)$remove;
    }

    /**
     * {@inheritdoc}
     */
    public function start()
    {
        $this->client = self::createPantherClient();
        $this->started = true;
    }

    /**
     * {@inheritdoc}
     */
    public function isStarted()
    {
        return $this->started;
    }

    /**
     * {@inheritdoc}
     */
    public function stop()
    {
        $this->reset();
        $this->started = false;
    }

    /**
     * {@inheritdoc}
     */
    public function reset()
    {
        // Restarting the client resets the cookies and the history
        $this->client->restart();
        $this->forms = array();
    }

    /**
     * {@inheritdoc}
     */
    public function visit($url)
    {
        $this->client->get($this->prepareUrl($url));
        $this->forms = array();
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentUrl()
    {
        return $this->client->getCurrentURL();
    }

    /**
     * {@inheritdoc}
     */
    public function reload()
    {
        $this->client->reload();
        $this->forms = array();
    }

    /**
     * {@inheritdoc}
     */
    public function forward()
    {
        $this->client->forward();
        $this->forms = array();
    }

    /**
     * {@inheritdoc}
     */
    public function back()
    {
        $this->client->back();
        $this->forms = array();
    }

    /**
     * {@inheritdoc}
     */
    public function switchToIFrame($name = null)
    {
        if (null === $name) {
            $this->client->switchTo()->defaultContent();
        } else {
            $this->client->switchTo()->frame($name);
        }
        $this->client->refreshCrawler();
    }

    /**
     * {@inheritdoc}
     */
    public function setCookie($name, $value = null)
    {
        if (null === $value) {
            $this->deleteCookie($name);

            return;
        }

        $jar = $this->client->getCookieJar();
        // @see: https://github.com/w3c/webdriver/issues/1238
        $jar->set(new Cookie($name, \rawurlencode((string)$value)));
    }

    /**
     * Deletes a cookie by name.
     *
     * @param string $name Cookie name.
     */
    private function deleteCookie($name)
    {
        $path = $this->getCookiePath();
        $jar = $this->client->getCookieJar();

        do {
            if (null !== $jar->get($name, $path)) {
                $jar->expire($name, $path);
            }

            $path = preg_replace('/.$/', '', $path);
        } while ($path);
    }

    /**
     * Returns current cookie path.
     *
     * @return string
     */
    private function getCookiePath()
    {
        $path = dirname(parse_url($this->getCurrentUrl(), PHP_URL_PATH));

        if ('\\' === DIRECTORY_SEPARATOR) {
            $path = str_replace('\\', '/', $path);
        }

        return $path;
    }

    /**
     * {@inheritdoc}
     */
    public function getCookie($name)
    {
        $cookies = $this->client->getCookieJar()->all();

        foreach ($cookies as $cookie) {
            if ($cookie->getName() === $name) {
                return \urldecode($cookie->getValue());
            }
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getContent()
    {
        return $this->getResponse()->getContent();
    }

    /**
     * {@inheritdoc}
     */
    public function getScreenshot($saveAs = null): string
    {
        return $this->client->takeScreenshot($saveAs);
    }

    /**
     * {@inheritdoc}
     */
    public function isVisible($xpath)
    {
        return $this->getCrawlerElement($this->getFilteredCrawler($xpath))->isDisplayed();
    }

    /**
     * {@inheritdoc}
     */
    public function isSelected($xpath)
    {
        return $this->getCrawlerElement($this->getFilteredCrawler($xpath))->isSelected();
    }

    /**
     * {@inheritdoc}
     */
    public function findElementXpaths($xpath)
    {
        $nodes = $this->getCrawler()->filterXPath($xpath);

        $elements = array();
        foreach ($nodes as $i => $node) {
            $elements[] = sprintf('(%s)[%d]', $xpath, $i + 1);
        }

        return $elements;
    }

    /**
     * {@inheritdoc}
     */
    public function getTagName($xpath)
    {
        return $this->getCrawlerElement($this->getFilteredCrawler($xpath))->getTagName();
    }

    /**
     * {@inheritdoc}
     */
    public function getText($xpath)
    {
        $text = $this->getFilteredCrawler($xpath)->text();
        $text = str_replace("\n", ' ', $text);
        $text = preg_replace('/ {2,}/', ' ', $text);

        return trim($text);
    }

    /**
     * {@inheritdoc}
     */
    public function getHtml($xpath)
    {
        // cut the tag itself (making innerHTML out of outerHTML)
        return preg_replace('/^\<[^\>]+\>|\<[^\>]+\>$/', '', $this->getOuterHtml($xpath));
    }

    /**
     * {@inheritdoc}
     */
    public function getOuterHtml($xpath)
    {
        $crawler = $this->getFilteredCrawler($xpath);

        return $crawler->html();
    }

    /**
     * {@inheritdoc}
     */
    public function getAttribute($xpath, $name)
    {
        $crawler = $this->getFilteredCrawler($xpath);

        $attribute = $this->getCrawlerElement($crawler)->getAttribute($name);

        return $attribute === '' ? null : $attribute;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue($xpath)
    {
        $element = $this->getCrawlerElement($this->getFilteredCrawler($xpath));
        $type = $this->getAttribute($xpath, 'type');
        if (in_array($type, array('submit', 'image', 'button'), true)) {
            return $element->getAttribute('value');
        }

        if ('checkbox' === $type) {
            $isChecked = $element->getAttribute('checked');

            return $isChecked ? $element->getAttribute('value') : null;
        }

        if ('option' === $element->getTagName()) {
            return $this->getOptionValue($element);
        }

        if ('input' === $element->getTagName()) {
            return $this->getOptionValue($element);
        }

        return $element->getAttribute('value');
    }

    /**
     * {@inheritdoc}
     */
    public function setValue($xpath, $value)
    {
        $element = $this->getCrawlerElement($this->getFilteredCrawler($xpath));
        $element->clear();

        $formField = $this->getFormField($xpath);
        $formField->setValue($value);
    }

    /**
     * {@inheritdoc}
     */
    public function check($xpath)
    {
        $this->getChoiceFormField($xpath)->tick();
    }

    /**
     * {@inheritdoc}
     */
    public function uncheck($xpath)
    {
        $this->getChoiceFormField($xpath)->untick();
    }

    /**
     * {@inheritdoc}
     */
    public function selectOption($xpath, $value, $multiple = false)
    {
        $field = $this->getFormField($xpath);

        if (!$field instanceof ChoiceFormField) {
            throw new DriverException(
                sprintf('Impossible to select an option on the element with XPath "%s" as it is not a select or radio input', $xpath)
            );
        }

        if ($multiple) {
            $oldValue = (array)$field->getValue();
            $oldValue[] = $value;
            $value = $oldValue;
        }

        $field->select($value);
    }

    /**
     * {@inheritdoc}
     */
    public function click($xpath)
    {
        $crawler = $this->getFilteredCrawler($xpath);
        $element = $this->getCrawlerElement($crawler);

        try {
            $link = new Link($element);
            $this->client->click($link);
            $this->client->refreshCrawler();
        } catch (\LogicException $e) {
            try {
                $form = new Form($element, $this->client->getWebDriver());
                $type = $element->getAttribute('type');
                if ('submit' === $type) {
                    $this->client->submit($form);
                    $this->client->refreshCrawler();
                }
            } catch (\LogicException $e) {
                // we are not clicking on a link
                $element->click();
                $this->client->refreshCrawler();
            }
        }

    }

    /**
     * {@inheritdoc}
     */
    public function isChecked($xpath)
    {
        $element = $this->getCrawlerElement($this->getFilteredCrawler($xpath));
        $type = $element->getAttribute('type');


        if ('radio' === $type) {
            return (bool) $element->getAttribute('value');
        }

        $choiceFormField = $this->getChoiceFormField($xpath);

        return $choiceFormField->hasValue();
    }

    /**
     * {@inheritdoc}
     */
    public function attachFile($xpath, $path)
    {
        $field = $this->getFormField($xpath);

        if (!$field instanceof FileFormField) {
            throw new DriverException(
                sprintf('Impossible to attach a file on the element with XPath "%s" as it is not a file input', $xpath)
            );
        }

        $field->upload($path);
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm($xpath)
    {
        $crawler = $this->getFilteredCrawler($xpath);

        $this->client->submit($crawler->form());
        $this->client->refreshCrawler();
        // $this->submit($crawler->form());
    }

    /**
     * @return Response
     *
     * @throws DriverException If there is not response yet
     */
    protected function getResponse()
    {
        $response = $this->client->getInternalResponse();

        if (null === $response) {
            throw new DriverException('Unable to access the response before visiting a page');
        }

        return $response;
    }

    /**
     * Prepares URL for visiting.
     * Removes "*.php/" from urls and then passes it to BrowserKitDriver::visit().
     *
     * @param string $url
     *
     * @return string
     */
    protected function prepareUrl($url)
    {
        $replacement = ($this->removeHostFromUrl ? '' : '$1').($this->removeScriptFromUrl ? '' : '$2');

        return preg_replace('#(https?\://[^/]+)(/[^/\.]+\.php)?#', $replacement, $url);
    }

    /**
     * Returns form field from XPath query.
     *
     * @param string $xpath
     *
     * @return FormField
     *
     * @throws DriverException
     */
    private function getFormField($xpath)
    {
        try {
            $formField = $this->getChoiceFormField($xpath);
        } catch (DriverException $e) {
            $formField = null;
        }
        if (!$formField) {
            try {
                $formField = $this->getInputFormField($xpath);
            } catch (DriverException $e) {
                $formField = null;
            }
        }
        if (!$formField) {
            try {
                $formField = $this->getFileFormField($xpath);
            } catch (DriverException $e) {
                $formField = null;
            }
        }
        if (!$formField) {
            $formField = $this->getTextareaFormField($xpath);
        }

        return $formField;
    }

    /**
     * Returns the checkbox field from xpath query, ensuring it is valid.
     *
     * @param string $xpath
     *
     * @return ChoiceFormField
     *
     * @throws DriverException when the field is not a checkbox
     */
    private function getChoiceFormField($xpath)
    {
        $element = $this->getCrawlerElement($this->getFilteredCrawler($xpath));
        try {
            $choiceFormField = new ChoiceFormField($element);
        } catch (\LogicException $e) {
            throw new DriverException(sprintf('Impossible to check the element with XPath "%s" as it is not a checkbox', $xpath));
        }

        return $choiceFormField;
    }

    /**
     * Returns the input field from xpath query, ensuring it is valid.
     *
     * @param string $xpath
     *
     * @return InputFormField
     *
     * @throws DriverException when the field is not a checkbox
     */
    private function getInputFormField($xpath)
    {
        $element = $this->getCrawlerElement($this->getFilteredCrawler($xpath));
        try {
            $inputFormField = new InputFormField($element);
        } catch (\LogicException $e) {
            throw new DriverException(sprintf('Impossible to check the element with XPath "%s" as it is not a checkbox', $xpath));
        }

        return $inputFormField;
    }

    /**
     * Returns the input field from xpath query, ensuring it is valid.
     *
     * @param string $xpath
     *
     * @return FileFormField
     *
     * @throws DriverException when the field is not a checkbox
     */
    private function getFileFormField($xpath)
    {
        $element = $this->getCrawlerElement($this->getFilteredCrawler($xpath));
        try {
            $fileFormField = new FileFormField($element);
        } catch (\LogicException $e) {
            throw new DriverException(sprintf('Impossible to check the element with XPath "%s" as it is not a checkbox', $xpath));
        }

        return $fileFormField;
    }

    /**
     * Returns the textarea field from xpath query, ensuring it is valid.
     *
     * @param string $xpath
     *
     * @return TextareaFormField
     *
     * @throws DriverException when the field is not a checkbox
     */
    private function getTextareaFormField($xpath)
    {
        $element = $this->getCrawlerElement($this->getFilteredCrawler($xpath));
        try {
            $textareaFormField = new TextareaFormField($element);
        } catch (\LogicException $e) {
            throw new DriverException(sprintf('Impossible to check the element with XPath "%s" as it is not a checkbox', $xpath));
        }

        return $textareaFormField;
    }

    /**
     * Gets the position of the field node among elements with the same name
     *
     * BrowserKit uses the field name as index to find the field in its Form object.
     * When multiple fields have the same name (checkboxes for instance), it will return
     * an array of elements in the order they appear in the DOM.
     *
     * @param \DOMElement $fieldNode
     *
     * @return integer
     */
    private function getFieldPosition(\DOMElement $fieldNode)
    {
        $elements = $this->getCrawler()->filterXPath('//*[@name=\''.$fieldNode->getAttribute('name').'\']');

        if (count($elements) > 1) {
            // more than one element contains this name !
            // so we need to find the position of $fieldNode
            foreach ($elements as $key => $element) {
                /** @var \DOMElement $element */
                if ($element->getNodePath() === $fieldNode->getNodePath()) {
                    return $key;
                }
            }
        }

        return 0;
    }

    private function submit(Form $form)
    {
        $formId = $this->getFormNodeId($form);

        if (isset($this->forms[$formId])) {
            $this->mergeForms($form, $this->forms[$formId]);
        }

        // remove empty file fields from request
        foreach ($form->getFiles() as $name => $field) {
            if (empty($field['name']) && empty($field['tmp_name'])) {
                $form->remove($name);
            }
        }

        foreach ($form->all() as $field) {
            // Add a fix for https://github.com/symfony/symfony/pull/10733 to support Symfony versions which are not fixed
            if ($field instanceof TextareaFormField && null === $field->getValue()) {
                $field->setValue('');
            }
        }

        $this->client->submit($form);

        $this->forms = array();
    }

    private function resetForm(WebDriverElement $element): void
    {
        $formId = $this->getFormNodeId($element);
        unset($this->forms[$formId]);
    }

    /**
     * Determines if an element can submit a form.
     *
     * @param WebDriverElement $element
     *
     * @return bool
     */
    private function canSubmitForm(WebDriverElement $element): bool
    {
        $type = $element->getAttribute('type');

        if ('input' === $element->getTagName() && in_array($type, array('submit', 'image'), true)) {
            return true;
        }

        return 'button' === $element->getTagName() && (null === $type || 'submit' === $type);
    }

    /**
     * Determines if a node can reset a form.
     *
     * @param WebDriverElement $element
     *
     * @return bool
     */
    private function canResetForm(WebDriverElement $element): bool
    {
        $type = $element->getAttribute('type');

        return in_array($element->getTagName(), array('input', 'button'), true) && 'reset' === $type;
    }

    /**
     * Returns form node unique identifier.
     *
     * @param WebDriverElement $form
     *
     * @return string
     */
    private function getFormNodeId(WebDriverElement $form): string
    {
        return \md5($form->getID());
    }

    /**
     * Gets the value of an option element
     *
     * @param WebDriverElement $option
     *
     * @return string
     *
     * @see \Symfony\Component\DomCrawler\Field\ChoiceFormField::buildOptionValue
     */
    private function getOptionValue(WebDriverElement $option): string
    {
        $value = $option->getAttribute('value');
        if ($value) {
            return $value;
        }

        throw new UnsupportedDriverActionException('Can not get value from option', $this);

        // if (!empty($option->nodeValue)) {
        //     return $option->nodeValue;
        // }
        //
        // return '1'; // DomCrawler uses 1 by default if there is no text in the option
    }

    /**
     * Merges second form values into first one.
     *
     * @param Form $to   merging target
     * @param Form $from merging source
     */
    private function mergeForms(Form $to, Form $from): void
    {
        foreach ($from->all() as $name => $field) {
            $fieldReflection = new \ReflectionObject($field);
            $nodeReflection = $fieldReflection->getProperty('node');
            $valueReflection = $fieldReflection->getProperty('value');

            $nodeReflection->setAccessible(true);
            $valueReflection->setAccessible(true);

            $isIgnoredField = $field instanceof InputFormField &&
                in_array($nodeReflection->getValue($field)->getAttribute('type'), array('submit', 'button', 'image'), true);

            if (!$isIgnoredField) {
                $valueReflection->setValue($to[$name], $valueReflection->getValue($field));
            }
        }
    }

    /**
     * Returns WebDriverElement from crawler instance.
     *
     * @param Crawler $crawler
     *
     * @return WebDriverElement
     *
     * @throws DriverException when the node does not exist
     */
    private function getCrawlerElement(Crawler $crawler): WebDriverElement
    {
        $node = $crawler->getElement(0);

        if (null !== $node) {
            return $node;
        }

        throw new DriverException('The element does not exist');
    }

    /**
     * Returns a crawler filtered for the given XPath, requiring at least 1 result.
     *
     * @param string $xpath
     *
     * @return Crawler
     *
     * @throws DriverException when no matching elements are found
     */
    private function getFilteredCrawler($xpath): Crawler
    {
        if (!count($crawler = $this->getCrawler()->filterXPath($xpath))) {
            throw new DriverException(sprintf('There is no element matching XPath "%s"', $xpath));
        }

        return $crawler;
    }

    /**
     * Returns crawler instance (got from client).
     *
     * @return Crawler
     *
     * @throws DriverException
     */
    private function getCrawler(): Crawler
    {
        $crawler = $this->client->getCrawler();

        if (null === $crawler) {
            throw new DriverException('Unable to access the response content before visiting a page');
        }

        return $crawler;
    }
}
