<?php

namespace Axelerant\ApplitoolsEyes\Driver;

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\WebDriver;
use Facebook\WebDriver\WebDriverBy;

class RemoteWebDriver implements WebDriver
{

    protected $webDriver;

    public function __construct(
      $wdHost = 'http://localhost:4444/wd/hub',
      $desiredCapabilities = null,
      $connectionTimeoutInMs = null,
      $requestTimeoutInMs = null,
      $httpProxy = null,
      $httpProxyPort = null,
      DesiredCapabilities $requiredCapabilities = null
    )
    {
        $this->webDriver = \Facebook\WebDriver\Remote\RemoteWebDriver::create(
          $wdHost,
          $desiredCapabilities,
          $connectionTimeoutInMs,
          $requestTimeoutInMs,
          $httpProxy,
          $httpProxyPort,
          $requiredCapabilities
        );
    }

    /**
     * @inheritDoc
     */
    public function close()
    {
        return $this->webDriver->close();
    }

    /**
     * @inheritDoc
     */
    public function get($url)
    {
        return $this->webDriver->get($url);
    }

    /**
     * @inheritDoc
     */
    public function getCurrentURL()
    {
        return $this->webDriver->getCurrentURL();
    }

    /**
     * @inheritDoc
     */
    public function getPageSource()
    {
        return $this->webDriver->getPageSource();
    }

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return $this->webDriver->getTitle();
    }

    /**
     * @inheritDoc
     */
    public function getWindowHandle()
    {
        return $this->webDriver->getWindowHandle();
    }

    /**
     * @inheritDoc
     */
    public function getWindowHandles()
    {
        return $this->webDriver->getWindowHandles();
    }

    /**
     * @inheritDoc
     */
    public function quit()
    {
        return $this->webDriver->quit();
    }

    /**
     * @inheritDoc
     */
    public function takeScreenshot($save_as = null)
    {
        return $this->webDriver->takeScreenshot($save_as);
    }

    /**
     * @inheritDoc
     */
    public function wait(
      $timeout_in_second = 30,
      $interval_in_millisecond = 250
    ) {
        return $this->webDriver->wait($timeout_in_second, $interval_in_millisecond);
    }

    /**
     * @inheritDoc
     */
    public function manage()
    {
        return $this->webDriver->manage();
    }

    /**
     * @inheritDoc
     */
    public function navigate()
    {
        return $this->webDriver->navigate();
    }

    /**
     * @inheritDoc
     */
    public function switchTo()
    {
        return $this->webDriver->switchTo();
    }

    /**
     * @inheritDoc
     */
    public function execute($name, $params)
    {
        return $this->webDriver->execute($name, $params);
    }

    /**
     * @inheritDoc
     */
    public function findElement(WebDriverBy $locator)
    {
        return $this->webDriver->findElement($locator);
    }

    /**
     * @inheritDoc
     */
    public function findElements(WebDriverBy $locator)
    {
        return $this->webDriver->findElements($locator);
    }
}
