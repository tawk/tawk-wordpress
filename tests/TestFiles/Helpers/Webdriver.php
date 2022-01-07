<?php

namespace Tawk\Tests\TestFiles\Helpers;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Firefox\FirefoxOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;

use Exception;

class Webdriver {
	private static function get_chrome_capabilities() {
		$capabilities = DesiredCapabilities::chrome();
		$options      = new ChromeOptions();
		$options->addArguments( array( '--headless' ) );
		$capabilities->setCapability( ChromeOptions::CAPABILITY_W3C, $options );

		return $capabilities;
	}

	private static function get_firefox_capabilities() {
		$capabilities = DesiredCapabilities::firefox();
		$options      = new FirefoxOptions();
		$options->addArguments( array( '--headless' ) );
		$capabilities->setCapability( FirefoxOptions::CAPABILITY, $options );

		return $capabilities;
	}

	private static function get_edge_capabilities() {
		return DesiredCapabilities::microsoftEdge();
	}

	public static function build_capabilities( string $browser ) {
		switch ( $browser ) {
			case 'firefox':
				return self::get_firefox_capabilities();
			case 'chrome':
				return self::get_chrome_capabilities();
			case 'edge':
				return self::get_edge_capabilities();
			default:
				throw new Exception( 'Browser not yet supported' );
		}
	}
}
