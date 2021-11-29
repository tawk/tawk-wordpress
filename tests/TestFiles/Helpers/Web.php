<?php

namespace Tawk\Tests\TestFiles\Helpers;

use Facebook\WebDriver\Remote\LocalFileDetector;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;

class Web {
	private $base_test_url;
	private $admin_url;
	private $plugin_page_url;
	private $plugin_settings_url;
	private $admin_user;
	private $admin_pass;
	private $tawk_user;
	private $tawk_pass;

	function __construct( &$driver, $config ) {
		$this->driver = $driver;

		$this->base_test_url = 'http://wordpress/';
		$this->admin_url = $this->base_test_url.'wp-admin/';
		$this->plugin_page_url = $this->admin_url.'plugins.php';
		$this->plugin_settings_url = $this->admin_url.'options-general.php?page=tawkto_plugin';
		$this->admin_user = $config['admin']['user'];
		$this->admin_pass = $config['admin']['pass'];
		$this->tawk_user = $config['tawk']['user'];
		$this->tawk_pass = $config['tawk']['pass'];
	}

	public function get_base_test_url () {
		return $this->base_test_url;
	}

	public function get_admin_url () {
		return $this->admin_url;
	}

	public function get_plugin_page_url () {
		return $this->plugin_page_url;
	}

	public function get_plugin_settings_url () {
		return $this->plugin_settings_url;
	}

	public function login() {
		$this->driver->get( $this->base_test_url.'wp-login.php' );

		$username = $this->driver->findElement( WebDriverBy::id('user_login') );
		$username->sendKeys( $this->admin_user );

		$password = $this->driver->findElement( WebDriverBy::id('user_pass') );
		$password->sendKeys( $this->admin_pass );

		$this->driver->findElement( WebDriverBy::id('wp-submit') )->click();
	}

	public function install_plugin() {
		$this->driver->get( $this->admin_url.'/plugin-install.php?tab=upload' );

		// find the upload input and send the zip file there
		$uploader = $this->driver->findElement( WebDriverBy::id( 'pluginzip' ) );
		$uploader->setFileDetector( new LocalFileDetector() );
		$uploader->sendKeys( getcwd().'/tawkto-live-chat.zip' ); // TODO: transfer this to config or set it as env var

		// find the install button and click
		$this->driver->findElement( WebDriverBy::id( 'install-plugin-submit' ) )->click();

		sleep( 1 ); // ensures that the installation is done
	}

	public function activate_plugin() {
		$this->driver->get( $this->plugin_page_url );

		// find the tawk.to plugin row
		$plugin = $this->driver->findElement( WebDriverBy::cssSelector( 'tr[data-slug="tawkto-live-chat"]' ) );
		$plugin->findElement( WebDriverBy::id( 'activate-tawkto-live-chat' ) )->click(); // this will reload the page
	}

	public function deactivate_plugin() {
		$this->driver->get( $this->plugin_page_url );

		$this->driver->findElement( WebDriverBy::id( 'deactivate-tawkto-live-chat' ) )->click();
	}

	public function uninstall_plugin() {
		$this->driver->get( $this->plugin_page_url );

		$this->driver->findElement( WebDriverBy::id( 'delete-tawkto-live-chat' ) )->click();

		$this->driver->wait()->until( WebDriverExpectedCondition::alertIsPresent() );
		$this->driver->switchTo()->alert()->accept();

		sleep( 1 ); // ensures that the deletion is done
	}

	public function goto_widget_selection() {
		// incase current frame is not on the default one
		$this->driver->switchTo()->defaultContent();

		$this->driver->get( $this->plugin_settings_url );

		$this->driver->findElement( WebDriverBy::id( 'account-settings-tab' ) )->click();

		// incase the "property and widget is already set" notice appears
		$reselect_link_els = $this->driver->findElements( WebDriverBy::id( 'reselect' ) );
		if ( 0 < count( $reselect_link_els ) ) {
			$reselect_link_els[0]->click();
		}

		$this->driver->wait(10, 100)->until(
			WebDriverExpectedCondition::frameToBeAvailableAndSwitchToIt(
				$this->driver->findElement( WebDriverBy::id( 'tawk-iframe' ) )
			)
		);

		// driver currently on tawk-iframe frame
		// incase the current session hasn't logged in to the plugin yet
		$login_form_els = $this->driver->findElements( WebDriverBy::id( 'loginForm' ) );
		if ( 0 < count( $login_form_els ) ) {
			$email = $login_form_els[0]->findElement( WebDriverBy::id( 'email' ) );
			$email->sendKeys( $this->tawk_user );

			$password = $login_form_els[0]->findElement( WebDriverBy::id( 'password' ) );
			$password->sendKeys( $this->tawk_pass );

			$login_form_els[0]->findElement( WebDriverBy::id( 'login-button' ) )->click();
		}
	}

	public function goto_visibility_options() {
		// incase current frame is not on the default one
		$this->driver->switchTo()->defaultContent();

		$this->driver->get( $this->plugin_settings_url );

		$this->driver->findElement( WebDriverBy::id( 'visibility-options-tab' ) )->click();
	}

	public function goto_privacy_options() {
		// incase current frame is not on the default one
		$this->driver->switchTo()->defaultContent();

		$this->driver->get( $this->plugin_settings_url );

		$this->driver->findElement( WebDriverBy::id( 'privacy-options-tab' ) )->click();
	}

	public function set_widget( $property_id, $widget_id ) {
		$this->goto_widget_selection();

		$widget_form = $this->driver->findElement( WebDriverBy::id( 'propertyForm' ) );

		$this->driver->findElement( WebDriverBy::id( 'property' ) )->click();
		$widget_form->findElement( WebDriverBy::cssSelector( 'li[data-id="'.$property_id.'"]' ) )->click();

		$this->driver->findElement( WebDriverBy::id( 'widget-'.$property_id ) )->click();
		$widget_form->findElement( WebDriverBy::cssSelector( 'li[data-id="'.$widget_id.'"]' ) )->click();

		$widget_form->findElement( WebDriverBy::id( 'addWidgetToPage' ) )->click();

		sleep( 1 ); // ensure add widget action finishes

		// go back to original frame
		$this->driver->switchTo()->defaultContent();
	}

	public function remove_widget() {
		$this->goto_widget_selection();

		$widget_form = $this->driver->findElement( WebDriverBy::id( 'propertyForm' ) );

		$widget_form->findElement( WebDriverBy::id( 'removeCurrentWidget' ) )->click();

		sleep( 1 ); // ensure remove widget action finishes

		// go back to original frame
		$this->driver->switchTo()->defaultContent();
	}
}
