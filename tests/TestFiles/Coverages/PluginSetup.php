<?php

namespace Tawk\Tests\TestFiles\Coverages;

use PHPUnit\Framework\TestCase;

use Facebook\WebDriver\WebDriverBy;

use Tawk\Tests\TestFiles\Helpers\Web;

abstract class PluginSetup extends TestCase {
	const BASE_TEST_URL = 'http://wordpress/'; // TODO: this needs to be shared across from a common file
	protected $driver;

	public function create_driver() {
		throw new Exception('Subclass should implement this');
	}

	public function setup(): void {
		$this->driver = $this->create_driver();

		Web::login($this->driver);

		$this->assertEquals( $this::BASE_TEST_URL.'wp-admin/', $this->driver->getCurrentURL() );
	}

	public function tearDown(): void {
		$this->driver->quit();
	}

	/**
	 * @test
	 */
	public function should_install_and_activate_plugin() {
		Web::install_plugin( $this->driver );

		Web::activate_plugin( $this->driver );

		$plugin = $this->driver->findElement( WebDriverBy::cssSelector( 'tr[data-slug="tawkto-live-chat"]' ) );
		$plugin_classes = explode( ' ', $plugin->getAttribute( 'class' ) );

		$this->assertTrue( in_array( 'active', $plugin_classes ) );

		$this->driver->get( $this::BASE_TEST_URL.'wp-admin/options-general.php?page=tawkto_plugin' );
		$this->assertTrue( 0 < count( $this->driver->findElements( WebDriverBy::id('tawk-settings-body' ) ) ) );
	}

	/**
	 * @test
	 */
	public function should_deactivate_and_uninstall_plugin() {
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
