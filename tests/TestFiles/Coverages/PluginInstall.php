<?php

namespace Tawk\Tests\TestFiles\Coverages;

use Facebook\WebDriver\WebDriverBy;

use Tawk\Tests\TestFiles\Helpers\Web;

abstract class PluginInstall extends BaseCoverage {
	public function setup(): void {
		$this->create_driver();

		Web::login( $this->driver );

		$this->assertEquals( $this::BASE_TEST_URL.'wp-admin/', $this->driver->getCurrentURL() );
	}

	public function tearDown(): void {
		try {
			Web::deactivate_plugin( $this->driver );
			Web::uninstall_plugin( $this->driver );
		} catch (Exception $e) {
			// Do nothing
		}

		$this->driver->quit();
	}

	/**
	 * @test
	 * @runInSeparateProcess
	 */
	public function should_install_and_activate_plugin(): void {
		Web::install_plugin( $this->driver );
		Web::activate_plugin( $this->driver );

		$plugin = $this->driver->findElement( WebDriverBy::cssSelector( 'tr[data-slug="tawkto-live-chat"]' ) );
		$plugin_classes = explode( ' ', $plugin->getAttribute( 'class' ) );
		$this->assertTrue( in_array( 'active', $plugin_classes ) );

		$this->driver->get( Web::PLUGIN_SETTINGS_URL );
		$this->assertTrue( 0 < count( $this->driver->findElements( WebDriverBy::id('tawk-settings-body' ) ) ) );
	}
}
