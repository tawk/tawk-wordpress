<?php

namespace Tawk\Tests\TestFiles\Helpers;

use Facebook\WebDriver\Remote\LocalFileDetector;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;

class Web {
	const BASE_TEST_URL = 'http://wordpress/'; // TODO: this needs to be shared across from a common file
	const PLUGIN_PAGE_URL = Web::BASE_TEST_URL.'wp-admin/plugins.php';
	const PLUGIN_SETTINGS_URL = Web::BASE_TEST_URL.'wp-admin/options-general.php?page=tawkto_plugin';

	public static function login( &$driver ) {
		$driver->get( Web::BASE_TEST_URL.'wp-login.php' );

		$username = $driver->findElement( WebDriverBy::id('user_login') );
		$username->sendKeys( 'admin' ); // TODO: transfer this to a config

		$password = $driver->findElement( WebDriverBy::id('user_pass') );
		$password->sendKeys( 'admin' ); // TODO: transfer this to a config

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
		$driver->get( Web::PLUGIN_PAGE_URL );

		// find the tawk.to plugin row
		$plugin = $driver->findElement( WebDriverBy::cssSelector( 'tr[data-slug="tawkto-live-chat"]' ) );
		$plugin->findElement(WebDriverBy::id('activate-tawkto-live-chat'))->click(); // this will reload the page
	}

	public static function deactivate_plugin( &$driver ) {
		$driver->get( Web::PLUGIN_PAGE_URL );

		$driver->findElement( WebDriverBy::id( 'deactivate-tawkto-live-chat' ) )->click();
	}

	public static function uninstall_plugin( &$driver ) {
		$driver->get( Web::PLUGIN_PAGE_URL );

		$driver->findElement( WebDriverBy::id( 'delete-tawkto-live-chat' ) )->click();

		$driver->wait()->until( WebDriverExpectedCondition::alertIsPresent() );
		$driver->switchTo()->alert()->accept();

		sleep( 1 ); // ensures that the deletion is done
	}

	public static function goto_widget_selection( &$driver ) {
		// incase current frame is not on the default one
		$driver->switchTo()->defaultContent();

		$driver->get( Web::PLUGIN_SETTINGS_URL );

		$driver->findElement( WebDriverBy::id( 'account-settings-tab' ) )->click();

		// incase the "property and widget is already set" notice appears
		$reselect_link_els = $driver->findElements( WebDriverBy::id( 'reselect' ) );
		if ( 0 < count( $reselect_link_els ) ) {
			$reselect_link_els[0]->click();
		}

		$driver->wait(10, 100)->until(
			WebDriverExpectedCondition::frameToBeAvailableAndSwitchToIt(
				$driver->findElement( WebDriverBy::id( 'tawk-iframe' ) )
			)
		);

		// driver currently on tawk-iframe frame
		// incase the current session hasn't logged in to the plugin yet
		$login_form_els = $driver->findElements( WebDriverBy::id( 'loginForm' ) );
		if ( 0 < count( $login_form_els ) ) {
			$email = $login_form_els[0]->findElement( WebDriverBy::id( 'email' ) );
			$email->sendKeys( 'tawkto@example.com' ); // TODO: move this to config

			$password = $login_form_els[0]->findElement( WebDriverBy::id( 'password' ) );
			$password->sendKeys( 'password' ); // TODO: move this to config

			$login_form_els[0]->findElement( WebDriverBy::id( 'login-button' ) )->click();
		}
	}

	public static function goto_visibility_options( &$driver ) {
		// incase current frame is not on the default one
		$driver->switchTo()->defaultContent();

		$driver->get( Web::PLUGIN_SETTINGS_URL );

		$driver->findElement( WebDriverBy::id( 'visibility-options-tab' ) )->click();
	}

	public static function goto_privacy_options( &$driver ) {
		// incase current frame is not on the default one
		$driver->switchTo()->defaultContent();

		$driver->get( Web::PLUGIN_SETTINGS_URL );

		$driver->findElement( WebDriverBy::id( 'privacy-options-tab' ) )->click();
	}

	public static function set_widget( &$driver, $property_id, $widget_id ) {
		Web::goto_widget_selection( $driver );

		$widget_form = $driver->findElement( WebDriverBy::id( 'propertyForm' ) );

		$driver->findElement( WebDriverBy::id( 'property' ) )->click();
		$widget_form->findElement( WebDriverBy::cssSelector( 'li[data-id="'.$property_id.'"]' ) )->click();

		$driver->findElement( WebDriverBy::id( 'widget-'.$property_id ) )->click();
		$widget_form->findElement( WebDriverBy::cssSelector( 'li[data-id="'.$widget_id.'"]' ) )->click();

		$widget_form->findElement( WebDriverBy::id( 'addWidgetToPage' ) )->click();

		sleep( 1 ); // ensure add widget action finishes

		// go back to original frame
		$driver->switchTo()->defaultContent();
	}

	public static function remove_widget( &$driver ) {
		Web::goto_widget_selection( $driver );

		$widget_form = $driver->findElement( WebDriverBy::id( 'propertyForm' ) );

		$widget_form->findElement( WebDriverBy::id( 'removeCurrentWidget' ) )->click();

		sleep( 1 ); // ensure remove widget action finishes

		// go back to original frame
		$driver->switchTo()->defaultContent();
	}
}
