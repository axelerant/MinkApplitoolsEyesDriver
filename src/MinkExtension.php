<?php

namespace Axelerant\ApplitoolsEyes;

use Axelerant\ApplitoolsEyes\Driver\RemoteWebDriverFactory;

class MinkExtension extends \Behat\MinkExtension\ServiceContainer\MinkExtension
{
    public function __construct()
    {
        parent::__construct();
        $this->registerDriverFactory(new RemoteWebDriverFactory());
    }
}
