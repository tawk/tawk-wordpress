<?php

namespace Tawk\Tests\Coverages;

use Facebook\WebDriver\WebDriverBy;

class PluginUninstallTest extends BaseCoverage {
	public function setup(): void {
		parent::setup();
		$this->web->login();

		$this->assertEquals( $this->web->get_admin_url(), $this->driver->getCurrentURL() );

		$this->web->install_plugin();
		$this->web->activate_plugin();
	}

	public function tearDown(): void {
		$this->driver->quit();
	}

	/**
	 * @test
	 */
	public function should_deactivate_and_uninstall_plugin(): void {
		$this->web->deactivate_plugin();

		$plugin = $this->driver->findElement( WebDriverBy::cssSelector( 'tr[data-slug="tawkto-live-chat"]' ) );
		$plugin_classes = explode( ' ', $plugin->getAttribute( 'class' ) );
		$this->assertTrue( in_array( 'inactive', $plugin_classes ) );

		$this->web->uninstall_plugin();

		$plugin = $this->driver->findElement( WebDriverBy::cssSelector( 'tr[data-slug="tawkto-live-chat"]' ) );
		$plugin_classes = explode( ' ', $plugin->getAttribute( 'class' ) );
		$this->assertTrue( in_array( 'deleted', $plugin_classes ) );
	}
}
