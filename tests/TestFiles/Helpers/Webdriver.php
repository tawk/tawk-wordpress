<?php

namespace Tawk\Tests\TestFiles\Helpers;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Firefox\FirefoxOptions;

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;

class Webdriver {
	public static function create_chrome() {
		$options = new ChromeOptions();
		$options->addArguments(array('--headless'));

		$capabilities = DesiredCapabilities::chrome();
		$capabilities->setCapability(ChromeOptions::CAPABILITY_W3C, $options);

		return RemoteWebDriver::create('chrome:4444', $capabilities);
	}

	public static function create_firefox() {
		$options = new FirefoxOptions();
		$options->addArguments(array('--headless'));

		$capabilities = DesiredCapabilities::firefox();
		$capabilities->setCapability(ChromeOptions::CAPABILITY, $options);

		return RemoteWebDriver::create('firefox:4444', $capabilities);
	}

	public static function create_driver( $browser ) {
		switch ( $browser ) {
			case 'firefox':
				return Webdriver::create_firefox();
			case 'chrome':
				return Webdriver::create_chrome();
			default:
				throw new Exception('Browser not yet supported');
		}
	}
}
