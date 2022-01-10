<?php

namespace Tawk\Tests\TestFiles\Helpers;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Firefox\FirefoxOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;

use Exception;
use Facebook\WebDriver\Remote\WebDriverCapabilityType;
use Facebook\WebDriver\WebDriverCapabilities;
use Facebook\WebDriver\WebDriverPlatform;

class Webdriver {
	private static function get_chrome_capabilities() {
		$capabilities = DesiredCapabilities::chrome();
		$options      = new ChromeOptions();
		$options->addArguments( array( '--headless' ) );
		$capabilities->setCapability( ChromeOptions::CAPABILITY_W3C, $options );
		$capabilities->setCapability( WebDriverCapabilityType::APPLICATION_CACHE_ENABLED, false );

		return $capabilities;
	}

	private static function get_firefox_capabilities() {
		$capabilities = DesiredCapabilities::firefox();
		$options      = new FirefoxOptions();
		$options->addArguments(
			array(
				'-headless',
				'--disable-dev-shm-usage',
				'--no-sandbox',
				'--disable-gpu',
				'--disable-setuid-sandbox',
			)
		);
		$capabilities->setCapability( FirefoxOptions::CAPABILITY, $options );
		$capabilities->setCapability( WebDriverCapabilityType::APPLICATION_CACHE_ENABLED, false );

		return $capabilities;
	}

	private static function get_edge_capabilities() {
		$capabilities = DesiredCapabilities::microsoftEdge();
		$capabilities->setCapability(
			'ms:edgeOptions',
			array(
				'args' => array(
					'--headless',
				),
			)
		);
		$capabilities->setCapability( WebDriverCapabilityType::PLATFORM, WebDriverPlatform::LINUX );
		$capabilities->setCapability( WebDriverCapabilityType::APPLICATION_CACHE_ENABLED, false );

		return $capabilities;
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
