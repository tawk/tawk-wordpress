<?php

namespace Tawk\Tests\Coverages;

use Facebook\WebDriver\WebDriverBy;

class PluginInstallTest extends BaseCoverage {
	public function setup(): void {
		parent::setup();
		$this->web->login();

		$this->assertEquals( $this->web->get_admin_url(), $this->driver->getCurrentURL() );
	}

	public function tearDown(): void {
		try {
			$this->web->deactivate_plugin();
			$this->web->uninstall_plugin();
		} catch (Exception $e) {
			// Do nothing
		}

		$this->driver->quit();
	}

	/**
	 * @test
	 */
	public function should_install_and_activate_plugin(): void {
		$this->web->install_plugin();
		$this->web->activate_plugin();

		$plugin = $this->driver->findElement( WebDriverBy::cssSelector( 'tr[data-slug="tawkto-live-chat"]' ) );
		$plugin_classes = explode( ' ', $plugin->getAttribute( 'class' ) );
		$this->assertTrue( in_array( 'active', $plugin_classes ) );

		$this->driver->get( $this->web->get_plugin_settings_url() );
		$this->assertTrue( 0 < count( $this->driver->findElements( WebDriverBy::id( 'tawk-settings-body' ) ) ) );
	}
}
