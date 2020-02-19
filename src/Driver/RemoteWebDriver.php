<?php

namespace Axelerant\ApplitoolsEyes\Driver;

use Behat\Mink\Driver\DriverInterface;
use Behat\Mink\Driver\Selenium2Driver;
use Behat\Mink\Session;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\DriverCommand;
use Facebook\WebDriver\Remote\HttpCommandExecutor;
use Facebook\WebDriver\Remote\RemoteWebDriver as BaseRemoteWebDriver;
use Facebook\WebDriver\Remote\WebDriverCommand;

class RemoteWebDriver extends BaseRemoteWebDriver implements DriverInterface
{

    protected $selenium2Driver;

    public function __construct(
      $selenium_server_url = 'http://localhost:4444/wd/hub',
      $desired_capabilities = null,
      $connection_timeout_in_ms = null,
      $request_timeout_in_ms = null,
      $http_proxy = null,
      $http_proxy_port = null,
      DesiredCapabilities $required_capabilities = null
    )
    {
        // BC layer to not break the method signature
        $selenium_server_url = preg_replace('#/+$#', '', $selenium_server_url);

        $desired_capabilities = self::castToDesiredCapabilitiesObject($desired_capabilities);

        $executor = new HttpCommandExecutor($selenium_server_url, $http_proxy, $http_proxy_port);
        if ($connection_timeout_in_ms !== null) {
            $executor->setConnectionTimeout($connection_timeout_in_ms);
        }
        if ($request_timeout_in_ms !== null) {
            $executor->setRequestTimeout($request_timeout_in_ms);
        }

        // W3C
        $parameters = [
          'capabilities' => [
            'firstMatch' => [$desired_capabilities->toW3cCompatibleArray()],
          ],
        ];

        if ($required_capabilities !== null && !empty($required_capabilities->toArray())) {
            $parameters['capabilities']['alwaysMatch'] = $required_capabilities->toW3cCompatibleArray();
        }

        // Legacy protocol
        if ($required_capabilities !== null) {
            // TODO: Selenium (as of v3.0.1) does accept requiredCapabilities only as a property of desiredCapabilities.
            // This has changed with the W3C WebDriver spec, but is the only way how to pass these
            // values with the legacy protocol.
            $desired_capabilities->setCapability('requiredCapabilities', $required_capabilities->toArray());
        }

        $parameters['desiredCapabilities'] = $desired_capabilities->toArray();

        $command = new WebDriverCommand(
          null,
          DriverCommand::NEW_SESSION,
          $parameters
        );

        $response = $executor->execute($command);
        $value = $response->getValue();

        if (!$isW3cCompliant = isset($value['capabilities'])) {
            $executor->disableW3cCompliance();
        }

        if ($isW3cCompliant) {
            $returnedCapabilities = DesiredCapabilities::createFromW3cCapabilities($value['capabilities']);
        } else {
            $returnedCapabilities = new DesiredCapabilities($value);
        }

        $this->executor = $executor;
        $this->sessionID = $response->getSessionID();
        $this->isW3cCompliant = $isW3cCompliant;

        if ($returnedCapabilities !== null) {
            $this->capabilities = $returnedCapabilities;
        }

        $this->selenium2Driver = new Selenium2Driver($desired_capabilities['browserName'], $desired_capabilities, $selenium_server_url);
    }

    /**
     * @inheritDoc
     */
    public function setSession(Session $session)
    {
        return $this->selenium2Driver->setSession($session);
    }

    /**
     * @inheritDoc
     */
    public function start()
    {
        return $this->selenium2Driver->start();
    }

    /**
     * @inheritDoc
     */
    public function isStarted()
    {
        return $this->selenium2Driver->isStarted();
    }

    /**
     * @inheritDoc
     */
    public function stop()
    {
        return $this->selenium2Driver->stop();
    }

    /**
     * @inheritDoc
     */
    public function reset()
    {
        return $this->selenium2Driver->reset();
    }

    /**
     * @inheritDoc
     */
    public function visit($url)
    {
        return $this->selenium2Driver->visit($url);
    }

    /**
     * @inheritDoc
     */
    public function reload()
    {
        return $this->selenium2Driver->reload();
    }

    /**
     * @inheritDoc
     */
    public function forward()
    {
        return $this->selenium2Driver->forward();
    }

    /**
     * @inheritDoc
     */
    public function back()
    {
        return $this->selenium2Driver->back();
    }

    /**
     * @inheritDoc
     */
    public function setBasicAuth($user, $password)
    {
        return $this->selenium2Driver->setBasicAuth($user, $password);
    }

    /**
     * @inheritDoc
     */
    public function switchToWindow($name = null)
    {
        return $this->selenium2Driver->switchToWindow($name);
    }

    /**
     * @inheritDoc
     */
    public function switchToIFrame($name = null)
    {
        return $this->selenium2Driver->switchToIFrame($name);
    }

    /**
     * @inheritDoc
     */
    public function setRequestHeader($name, $value)
    {
        return $this->selenium2Driver->setRequestHeader($name, $value);
    }

    /**
     * @inheritDoc
     */
    public function getResponseHeaders()
    {
        return $this->selenium2Driver->getResponseHeaders();
    }

    /**
     * @inheritDoc
     */
    public function setCookie($name, $value = null)
    {
        return $this->selenium2Driver->setCookie($name, $value);
    }

    /**
     * @inheritDoc
     */
    public function getCookie($name)
    {
        return $this->selenium2Driver->getCookie($name);
    }

    /**
     * @inheritDoc
     */
    public function getStatusCode()
    {
        return $this->selenium2Driver->getStatusCode();
    }

    /**
     * @inheritDoc
     */
    public function getContent()
    {
        return $this->selenium2Driver->getContent();
    }

    /**
     * @inheritDoc
     */
    public function getScreenshot()
    {
        return $this->selenium2Driver->getScreenshot();
    }

    /**
     * @inheritDoc
     */
    public function getWindowNames()
    {
        return $this->selenium2Driver->getWindowNames();
    }

    /**
     * @inheritDoc
     */
    public function getWindowName()
    {
        return $this->selenium2Driver->getWindowName();
    }

    /**
     * @inheritDoc
     */
    public function find($xpath)
    {
        return $this->selenium2Driver->find($xpath);
    }

    /**
     * @inheritDoc
     */
    public function getTagName($xpath)
    {
        return $this->selenium2Driver->getTagName($xpath);
    }

    /**
     * @inheritDoc
     */
    public function getText($xpath)
    {
        return $this->selenium2Driver->getText($xpath);
    }

    /**
     * @inheritDoc
     */
    public function getHtml($xpath)
    {
        return $this->selenium2Driver->getHtml($xpath);
    }

    /**
     * @inheritDoc
     */
    public function getOuterHtml($xpath)
    {
        return $this->selenium2Driver->getOuterHtml($xpath);
    }

    /**
     * @inheritDoc
     */
    public function getAttribute($xpath, $name)
    {
        return $this->selenium2Driver->getAttribute($xpath, $name);
    }

    /**
     * @inheritDoc
     */
    public function getValue($xpath)
    {
        return $this->selenium2Driver->getValue($xpath);
    }

    /**
     * @inheritDoc
     */
    public function setValue($xpath, $value)
    {
        return $this->selenium2Driver->setValue($xpath, $value);
    }

    /**
     * @inheritDoc
     */
    public function check($xpath)
    {
        return $this->selenium2Driver->check($xpath);
    }

    /**
     * @inheritDoc
     */
    public function uncheck($xpath)
    {
        return $this->selenium2Driver->uncheck($xpath);
    }

    /**
     * @inheritDoc
     */
    public function isChecked($xpath)
    {
        return $this->selenium2Driver->isChecked($xpath);
    }

    /**
     * @inheritDoc
     */
    public function selectOption($xpath, $value, $multiple = false)
    {
        return $this->selenium2Driver->selectOption($xpath, $value, $multiple);
    }

    /**
     * @inheritDoc
     */
    public function isSelected($xpath)
    {
        return $this->selenium2Driver->isSelected($xpath);
    }

    /**
     * @inheritDoc
     */
    public function click($xpath)
    {
        return $this->selenium2Driver->click($xpath);
    }

    /**
     * @inheritDoc
     */
    public function doubleClick($xpath)
    {
        return $this->selenium2Driver->doubleClick($xpath);
    }

    /**
     * @inheritDoc
     */
    public function rightClick($xpath)
    {
        return $this->selenium2Driver->rightClick($xpath);
    }

    /**
     * @inheritDoc
     */
    public function attachFile($xpath, $path)
    {
        return $this->selenium2Driver->attachFile($xpath, $path);
    }

    /**
     * @inheritDoc
     */
    public function isVisible($xpath)
    {
        return $this->selenium2Driver->isVisible($xpath);
    }

    /**
     * @inheritDoc
     */
    public function mouseOver($xpath)
    {
        return $this->selenium2Driver->mouseOver($xpath);
    }

    /**
     * @inheritDoc
     */
    public function focus($xpath)
    {
        return $this->selenium2Driver->focus($xpath);
    }

    /**
     * @inheritDoc
     */
    public function blur($xpath)
    {
        return $this->selenium2Driver->blur($xpath);
    }

    /**
     * @inheritDoc
     */
    public function keyPress($xpath, $char, $modifier = null)
    {
        return $this->selenium2Driver->keyPress($xpath, $char, $modifier);
    }

    /**
     * @inheritDoc
     */
    public function keyDown($xpath, $char, $modifier = null)
    {
        return $this->selenium2Driver->keyDown($xpath, $char, $modifier);
    }

    /**
     * @inheritDoc
     */
    public function keyUp($xpath, $char, $modifier = null)
    {
        return $this->selenium2Driver->keyUp($xpath, $char, $modifier);
    }

    /**
     * @inheritDoc
     */
    public function dragTo($sourceXpath, $destinationXpath)
    {
        return $this->selenium2Driver->dragTo($sourceXpath, $destinationXpath);
    }

    /**
     * @inheritDoc
     */
    public function evaluateScript($script)
    {
        return $this->selenium2Driver->evaluateScript($script);
    }

    /**
     * @inheritDoc
     */
    public function resizeWindow($width, $height, $name = null)
    {
        return $this->selenium2Driver->resizeWindow($width, $height, $name);
    }

    /**
     * @inheritDoc
     */
    public function maximizeWindow($name = null)
    {
        return $this->selenium2Driver->maximizeWindow($name);
    }

    /**
     * @inheritDoc
     */
    public function submitForm($xpath)
    {
        return $this->selenium2Driver->submitForm($xpath);
    }
}
