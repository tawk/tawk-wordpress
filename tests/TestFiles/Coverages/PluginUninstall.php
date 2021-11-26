<?php

namespace Tawk\Tests\TestFiles\Coverages;

use Facebook\WebDriver\WebDriverBy;

use Tawk\Tests\TestFiles\Helpers\Web;

abstract class PluginUninstall extends BaseCoverage {
	public function setup(): void {
		$this->create_driver();

		Web::login( $this->driver );

		$this->assertEquals( $this::BASE_TEST_URL.'wp-admin/', $this->driver->getCurrentURL() );

		Web::install_plugin( $this->driver );
		Web::activate_plugin( $this->driver );
	}

	public function tearDown(): void {
		$this->driver->quit();
	}

	/**
	 * @test
	 * @runInSeparateProcess
	 */
	public function should_deactivate_and_uninstall_plugin(): void {
		Web::deactivate_plugin( $this->driver );

		$plugin = $this->driver->findElement( WebDriverBy::cssSelector( 'tr[data-slug="tawkto-live-chat"]' ) );
		$plugin_classes = explode( ' ', $plugin->getAttribute( 'class' ) );
		$this->assertTrue( in_array( 'inactive', $plugin_classes ) );

		Web::uninstall_plugin( $this->driver );

		$plugin = $this->driver->findElement( WebDriverBy::cssSelector( 'tr[data-slug="tawkto-live-chat"]' ) );
		$plugin_classes = explode( ' ', $plugin->getAttribute( 'class' ) );
		$this->assertTrue( in_array( 'deleted', $plugin_classes ) );
	}
}
