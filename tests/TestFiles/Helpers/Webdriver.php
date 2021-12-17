<?php

namespace Tawk\Tests\TestFiles\Helpers;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Firefox\FirefoxOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;

use Tawk\Tests\TestFiles\Types\BrowserStackConfig;

use Exception;

class Webdriver {
	private static function get_bs_common_capabilities(
		string $session_name,
		BrowserStackConfig $browserstack
	) {
		$capabilities = array(
			'local'        => 'true',
			'maskCommands' => 'setValues',
		);

		$capabilities['sessionName']     = $session_name;
		$capabilities['userName']        = $browserstack->username;
		$capabilities['accessKey']       = $browserstack->access_key;
		$capabilities['localIdentifier'] = $browserstack->local_identifier;
		$capabilities['buildName']       = $browserstack->build_name;
		$capabilities['projectName']     = $browserstack->project_name;

		return $capabilities;
	}

	private static function get_chrome_capabilities(
		string $session_name,
		BrowserStackConfig $browserstack
	) {
		if ( true === $browserstack->is_browserstack ) {
			// TODO: transfer os, os_version, browser, and browser_version to config.
			return array(
				'bstack:options' => array_merge(
					self::get_bs_common_capabilities( $session_name, $browserstack ),
					array(
						'os'        => 'Windows',
						'osVersion' => '10',
					),
				),
				'browserName'    => 'Chrome',
				'browserVersion' => 'latest',
			);
		}

		$capabilities = DesiredCapabilities::chrome();
		$options      = new ChromeOptions();
		$options->addArguments( array( '--headless' ) );
		$capabilities->setCapability( ChromeOptions::CAPABILITY_W3C, $options );

		return $capabilities;
	}

	private static function get_firefox_capabilities(
		string $session_name,
		BrowserStackConfig $browserstack
	) {
		if ( true === $browserstack->is_browserstack ) {
			// TODO: transfer os, os_version, browser, and browser_version to config.
			return array(
				'bstack:options' => array_merge(
					self::get_bs_common_capabilities( $session_name, $browserstack ),
					array(
						'os'        => 'Windows',
						'osVersion' => '10',
					),
				),
				'browserName'    => 'Firefox',
				'browserVersion' => 'latest',
			);
		}

		$capabilities = DesiredCapabilities::firefox();
		$options      = new FirefoxOptions();
		$options->addArguments( array( '--headless' ) );
		$capabilities->setCapability( FirefoxOptions::CAPABILITY, $options );

		return $capabilities;
	}

	private static function get_safari_capabilities(
		string $session_name,
		BrowserStackConfig $browserstack
	) {
		// TODO: transfer os, os_version, browser, and browser_version to config.
		return array(
			'bstack:options' => array_merge(
				self::get_bs_common_capabilities( $session_name, $browserstack ),
				array(
					'os'        => 'OS X',
					'osVersion' => 'Monterey',
					'safari'    => array(
						'allowAllCookies' => 'true',
					),
				),
			),
			'browserName'    => 'Safari',
			'browserVersion' => '15.0',
		);
	}

	public static function build_capabilities(
		string $browser,
		string $session_name,
		BrowserStackConfig $browserstack
	) {
		switch ( $browser ) {
			case 'firefox':
				return self::get_firefox_capabilities( $session_name, $browserstack );
			case 'chrome':
				return self::get_chrome_capabilities( $session_name, $browserstack );
			case 'safari':
				if ( false === $browserstack->is_browserstack ) {
					throw new Exception( 'Safari tests are only supported by browserstack' );
				}

				// only have browserstack for safari.
				return self::get_safari_capabilities( $session_name, $browserstack );
			default:
				throw new Exception( 'Browser not yet supported' );
		}
	}
}
