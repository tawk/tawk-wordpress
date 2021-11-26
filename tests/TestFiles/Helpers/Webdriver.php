<?php

namespace Tawk\Tests\TestFiles\Helpers;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Firefox\FirefoxOptions;

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;

class Webdriver {
	public static function createChrome() {
		$options = new ChromeOptions();
        $options->addArguments(array('--headless'));

        $capabilities = DesiredCapabilities::chrome();
        $capabilities->setCapability(ChromeOptions::CAPABILITY_W3C, $options);

        return RemoteWebDriver::create('selenium:4444', $capabilities);
	}

	public static function createFirefox() {
		$options = new FirefoxOptions();
        $options->addArguments(array('--headless'));

        $capabilities = DesiredCapabilities::firefox();
        $capabilities->setCapability(ChromeOptions::CAPABILITY, $options);

        return RemoteWebDriver::create('selenium:4444', $capabilities);
	}
}
