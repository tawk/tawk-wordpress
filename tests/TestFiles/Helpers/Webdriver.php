<?php

namespace Tawk\Tests\TestFiles\Helpers;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Firefox\FirefoxOptions;

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;

class Webdriver {
	public static function create_chrome( $selenium_url ) {
		$options = new ChromeOptions();
		$options->addArguments(array('--headless'));

		$capabilities = DesiredCapabilities::chrome();
		$capabilities->setCapability(ChromeOptions::CAPABILITY_W3C, $options);

		return RemoteWebDriver::create( $selenium_url, $capabilities );
	}

	public static function create_firefox( $selenium_url ) {
		$options = new FirefoxOptions();
		$options->addArguments(array('--headless'));

		$capabilities = DesiredCapabilities::firefox();
		$capabilities->setCapability(ChromeOptions::CAPABILITY, $options);

		return RemoteWebDriver::create( $selenium_url, $capabilities );
	}

	public static function create_driver( $browser, $selenium_url ) {
		switch ( $browser ) {
			case 'firefox':
				return Webdriver::create_firefox( $selenium_url );
			case 'chrome':
				return Webdriver::create_chrome( $selenium_url );
			default:
				throw new Exception('Browser not yet supported');
		}
	}
}
