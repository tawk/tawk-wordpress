<?php

namespace Tawk\Tests\TestFiles\Helpers;

use Facebook\WebDriver\Remote\LocalFileDetector;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;

class Web {
	const BASE_TEST_URL = 'http://wordpress/'; // TODO: this needs to be shared across from a common file

	public static function login( &$driver ) {
		$driver->get( Web::BASE_TEST_URL.'wp-login.php' );

		$username = $driver->findElement( WebDriverBy::id('user_login') );
		$username->sendKeys( 'admin' );

		$password = $driver->findElement( WebDriverBy::id('user_pass') );
		$password->sendKeys( 'admin' );

		$driver->findElement( WebDriverBy::id('wp-submit') )->click();
	}

	public static function install_plugin( &$driver ) {
		$driver->get( Web::BASE_TEST_URL.'wp-admin/plugin-install.php?tab=upload' );

		// find the upload input and send the zip file there
		$uploader = $driver->findElement( WebDriverBy::id( 'pluginzip' ) );
		$uploader->setFileDetector( new LocalFileDetector() );
		$uploader->sendKeys( getcwd().'/tawkto-live-chat.zip' );

		// find the install button and click
		$driver->findElement( WebDriverBy::id( 'install-plugin-submit' ) )->click();

		sleep( 1 ); // ensures that the installation is done
	}

	public static function activate_plugin( &$driver ) {
		$driver->get( Web::BASE_TEST_URL.'wp-admin/plugins.php' );

		// find the tawk.to plugin row
		$plugin = $driver->findElement( WebDriverBy::cssSelector( 'tr[data-slug="tawkto-live-chat"]' ) );
		$plugin->findElement(WebDriverBy::id('activate-tawkto-live-chat'))->click(); // this will reload the page
	}

	public static function deactivate_plugin( &$driver ) {
		$driver->get( Web::BASE_TEST_URL.'wp-admin/plugins.php' );

		$driver->findElement( WebDriverBy::id( 'deactivate-tawkto-live-chat' ) )->click();
	}

	public static function uninstall_plugin( &$driver ) {
		$driver->get( Web::BASE_TEST_URL.'wp-admin/plugins.php' );

		$driver->findElement( WebDriverBy::id( 'delete-tawkto-live-chat' ) )->click();

		$driver->wait()->until( WebDriverExpectedCondition::alertIsPresent() );
		$driver->switchTo()->alert()->accept();

		sleep(1); // ensures that the deletion is done
	}
}
