<?php

namespace Tawk\Tests\TestFiles\Modules;

use Tawk\Tests\TestFiles\Types\TawkConfig;

use Tawk\Tests\TestFiles\Helpers\Common;
use Tawk\Tests\TestFiles\Types\Web\WebConfiguration;
use Tawk\Tests\TestFiles\Types\Web\WebDependencies;
use Tawk\Tests\TestFiles\Types\WebUserConfig;

class Web {
	private Webdriver $driver;
	private string $base_url;
	private string $admin_url;
	private string $plugin_page_url;
	private string $plugin_settings_url;
	private WebUserConfig $admin;
	private TawkConfig $tawk;

	private bool $logged_in;
	private bool $plugin_installed;
	private bool $plugin_activated;
	private bool $widget_set;

	public function __construct( WebDependencies $dependencies, WebConfiguration $config ) {
		$this->driver = $dependencies->driver;

		$this->base_url            = Common::build_url( $config->web->url );
		$this->admin_url           = $this->base_url . 'wp-admin/';
		$this->plugin_page_url     = $this->admin_url . 'plugins.php';
		$this->plugin_settings_url = $this->admin_url . 'options-general.php?page=tawkto_plugin';

		$this->admin = $config->web->admin;
		$this->tawk  = $config->tawk;

		$this->logged_in        = false;
		$this->plugin_installed = false;
		$this->plugin_activated = false;
		$this->widget_set       = false;
	}

	public function get_base_url() {
		return $this->base_url;
	}

	public function get_admin_url() {
		return $this->admin_url;
	}

	public function get_plugin_page_url() {
		return $this->plugin_page_url;
	}

	public function get_plugin_settings_url() {
		return $this->plugin_settings_url;
	}

	public function login() {
		if ( true === $this->logged_in ) {
			$this->driver->goto_page( $this->admin_url );
			return;
		}

		$this->driver->get_driver()->manage()->deleteAllCookies();

		$this->driver->goto_page( $this->base_url . 'wp-login.php' );

		$this->driver->find_element_and_input( '#user_login', $this->admin->username );
		$this->driver->find_element_and_input( '#user_pass', $this->admin->password );
		$this->driver->find_element_and_click( '#wp-submit' );

		$this->driver->wait_until_page_fully_loads();

		// prevent reauth=1 loop.
		$this->driver->goto_page( $this->plugin_page_url );
		$current_url = $this->driver->get_current_url();
		if ( false !== strpos( $current_url, 'reauth=1' ) ) {
			return $this->login();
		}

		$this->logged_in = true;
	}

	public function logout() {
		if ( false === $this->logged_in ) {
			return;
		}

		$this->driver->get_driver()->navigate()->refresh();

		$logout_selector = '#wp-admin-bar-logout > a';
		$logout_url      = $this->driver->find_element_and_get_attribute_value( $logout_selector, 'href' );

		$this->driver->get_driver()->get( $logout_url );

		$this->logged_in = false;
	}

	public function install_plugin() {
		if ( true === $this->plugin_installed ) {
			return;
		}

		$file_upload_id = '#pluginzip';
		$file_path      = getcwd() . '/tmp/tawkto-live-chat.zip';

		$this->driver->goto_page( $this->admin_url . 'plugin-install.php?tab=upload' );
		$this->driver->wait_until_element_is_located( $file_upload_id );

		// find the upload input and send the zip file there.
		$this->driver->upload_file( $file_upload_id, $file_path );

		// find the install button and click.
		$this->driver->find_element_and_click( '#install-plugin-submit' );
		$this->driver->wait_until_url_contains( $this->admin_url . 'update.php?action=upload-plugin' );

		// this ensures that the plugin's installed.
		$this->driver->wait_until_element_text_contains( 'a.button.button-primary', 'Activate' );

		$this->plugin_installed = true;
	}

	public function activate_plugin() {
		if ( true === $this->plugin_activated ) {
			return;
		}

		$this->driver->goto_page( $this->plugin_page_url );

		$activate_id = '#activate-tawkto-live-chat';
		$this->driver->find_element_and_click( $activate_id );

		$this->plugin_activated = true;
	}

	public function deactivate_plugin() {
		if ( false === $this->plugin_activated ) {
			return;
		}

		$this->driver->goto_page( $this->plugin_page_url );

		$activate_id = '#deactivate-tawkto-live-chat';
		$this->driver->find_element_and_click( $activate_id );

		$this->plugin_activated = false;
	}

	public function uninstall_plugin() {
		if ( false === $this->plugin_installed ) {
			return;
		}

		$this->driver->goto_page( $this->plugin_page_url );

		$activate_id = '#delete-tawkto-live-chat';
		$this->driver->find_element_and_click( $activate_id );

		$this->driver->wait_for_alert_and_accept();

		$this->driver->wait_until_element_is_located( '#tawkto-live-chat-deleted' );

		$this->plugin_installed = false;
	}

	public function goto_widget_selection() {
		// incase current frame is not on the default one.
		$this->driver->switch_to_default_frame();

		$this->driver->goto_page( $this->plugin_settings_url );

		$tab_id = '#account-settings-tab';
		$this->driver->find_element_and_click( $tab_id );

		// incase the "property and widget is already set" notice appears.
		$reselect_link_id = '#reselect';
		$reselect_link    = $this->driver->find_and_check_element( $reselect_link_id );
		if ( false === is_null( $reselect_link ) ) {
			$reselect_link->click();
		}

		$this->driver->wait_for_frame_and_switch( '#tawk-iframe', 10 );

		// driver currently on tawk-iframe frame
		// incase the current session hasn't logged in to the plugin yet.
		$login_button_id = '#login-button';
		$login_button    = $this->driver->find_and_check_element( $login_button_id );
		if ( true === is_null( $login_button ) ) {
			return;
		}

		$this->driver->find_element_and_click( '#login-button' );

		$window_handles = $this->driver->get_driver()->getWindowHandles();
		$this->driver->get_driver()->switchTo()->window( end( $window_handles ) );

		// driver currently on tawk.to OAuth login popout.

		// handle currently logged in page.
		$allow_id     = '#allow';
		$allow_button = $this->driver->find_and_check_element( $allow_id );
		if ( false === is_null( $allow_button ) ) {
			$allow_button->click();
			$this->driver->get_driver()->switchTo()->window( reset( $window_handles ) );
			$this->driver->wait_for_frame_and_switch( '#tawk-iframe', 10 );
			return;
		}

		// handle login page.
		$this->driver->find_element_and_input( '#email', $this->tawk->username );
		$this->driver->find_element_and_input( '#password', $this->tawk->password );
		$this->driver->find_element_and_click( 'button[type="submit"]' );

		// handle consent page.
		$this->driver->wait_for_seconds( 3 );
		$allow_id     = '#allow';
		$allow_button = $this->driver->find_and_check_element( $allow_id );

		if ( false === is_null( $allow_button ) ) {
			$allow_button->click();
		}

		// go back to tawk-iframe frame.
		$this->driver->get_driver()->switchTo()->window( reset( $window_handles ) );
		$this->driver->wait_for_frame_and_switch( '#tawk-iframe', 10 );
	}

	public function goto_visibility_options() {
		// incase current frame is not on the default one.
		$this->driver->switch_to_default_frame();

		$this->driver->goto_page( $this->plugin_settings_url );

		$tab_id = '#visibility-options-tab';
		$this->driver->find_element_and_click( $tab_id );
	}

	public function goto_privacy_options() {
		// incase current frame is not on the default one.
		$this->driver->switch_to_default_frame();

		$this->driver->goto_page( $this->plugin_settings_url );

		$tab_id = '#privacy-options-tab';
		$this->driver->find_element_and_click( $tab_id );
	}

	public function goto_woocommerce_options() {
		// incase current frame is not on the default one.
		$this->driver->switch_to_default_frame();

		$this->driver->goto_page( $this->plugin_settings_url );

		$tab_id = '#woocommerce-options-tab';
		$this->driver->find_element_and_click( $tab_id );
	}

	public function set_widget( $property_id, $widget_id ) {
		if ( true === $this->widget_set ) {
			return;
		}

		$this->goto_widget_selection();

		$property_form_id = '#propertyForm';
		$this->driver->wait_until_element_is_located( $property_form_id );
		$this->driver->find_element_and_click( '#property' );
		$this->driver->find_element_and_click( 'li[data-id="' . $property_id . '"]' );
		$this->driver->find_element_and_click( '#widget-' . $property_id );
		$this->driver->find_element_and_click( 'li[data-id="' . $widget_id . '"]' );
		$this->driver->find_element_and_click( '#addWidgetToPage' );

		// ensures widget is added.
		$this->driver->wait_until_element_is_located( '#successMessage' );

		$this->widget_set = true;

		// go back to original frame.
		$this->driver->switch_to_default_frame();
	}

	public function remove_widget() {
		if ( false === $this->widget_set ) {
			return;
		}

		$this->goto_widget_selection();

		$this->driver->wait_until_element_is_located( '#propertyForm' );
		$this->driver->find_element_and_click( '#removeCurrentWidget' );

		// ensures widget is added.
		$this->driver->wait_until_element_is_located( '#successMessage' );

		$this->widget_set = false;

		// go back to original frame.
		$this->driver->switch_to_default_frame();
	}

	public function reset_visibility_options( $save_flag = true ) {
		$this->goto_visibility_options();
		$this->toggle_switch( '#always-display', false );

		// clear out the text areas first.
		$this->toggle_switch( '#exclude-url', true );
		$this->driver->clear_input( '#excluded-url-list' );
		$this->toggle_switch( '#include-url', true );
		$this->driver->clear_input( '#included-url-list' );

		// disable all other toggles except for always display.
		$this->toggle_switch( '#show-onfrontpage', false );
		$this->toggle_switch( '#show-oncategory', false );
		$this->toggle_switch( '#show-ontagpage', false );
		$this->toggle_switch( '#show-onarticlepages', false );
		$this->toggle_switch( '#exclude-url', false );
		$this->toggle_switch( '#include-url', false );
		$this->toggle_switch( '#always-display', true );

		if ( $save_flag ) {
			$this->driver->move_mouse_to( '#submit-header' )->click();
			$this->driver->wait_until_element_is_located( '#setting-error-settings_updated' );
		}
	}

	public function reset_woocommerce_options( $save_flag = true ) {
		$this->goto_woocommerce_options();
		$this->toggle_switch( '#display-on-shop', false );
		$this->toggle_switch( '#display-on-productcategory', false );
		$this->toggle_switch( '#display-on-productpage', false );
		$this->toggle_switch( '#display-on-producttag', false );

		if ( $save_flag ) {
			$this->driver->move_mouse_to( '#submit-header' )->click();
			$this->driver->wait_until_element_is_located( '#setting-error-settings_updated' );
		}
	}

	public function toggle_switch( $field_id, $enabled_flag ) {
		$checkbox = $this->driver->find_element( $field_id );

		if ( $checkbox->isSelected() === $enabled_flag ) {
			return;
		}

		$slider_selector = $field_id . ' + .slider.round';
		$this->driver->move_mouse_to( $slider_selector )->click();
	}
}
