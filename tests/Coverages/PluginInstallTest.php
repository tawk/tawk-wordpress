<?php

namespace Tawk\Tests\Coverages;

use PHPUnit\Framework\TestCase;

use Tawk\Tests\TestFiles\Enums\BrowserStackStatus;
use Tawk\Tests\TestFiles\Config;
use Tawk\Tests\TestFiles\Helpers\Common;
use Tawk\Tests\TestFiles\Modules\Web;
use Tawk\Tests\TestFiles\Modules\Webdriver;

class PluginInstallTest extends TestCase {
	protected static Webdriver $driver;
	protected static Web $web;

	public static function setupBeforeClass(): void {
		$config = Config::get_config();

		self::$driver = Common::create_driver( 'Plugin Install Test', $config );
		self::$web = Common::create_web( self::$driver, $config );
	}

	public function setup(): void {
		self::$web->login();

		$this->assertEquals( self::$web->get_admin_url(), self::$driver->get_current_url() );
	}

	protected function onNotSuccessfulTest( $err ): void {
		self::$driver->update_test_status( BrowserStackStatus::FAILED, $err->getMessage());
		throw $err;
	}

	public static function tearDownAfterClass(): void {
		self::$web->deactivate_plugin();
		self::$web->uninstall_plugin();

		self::$driver->quit();
	}

	/**
	 * @test
	 */
	public function should_install_and_activate_plugin(): void {
		self::$web->install_plugin();
		self::$web->activate_plugin();


		$plugin_row_selector = 'tr[data-slug="tawkto-live-chat"]';
		self::$driver->wait_until_element_is_located( $plugin_row_selector );
		$plugin_classes = explode(
			' ',
			self::$driver->find_element_and_get_attribute_value(
				$plugin_row_selector,
				'class'
			)
		);
		$this->assertTrue( in_array( 'active', $plugin_classes ) );

		self::$driver->goto_page( self::$web->get_plugin_settings_url() );

		$settings_body_id = '#tawk-settings-body';
		self::$driver->wait_until_element_is_located( $settings_body_id );

		$settings_body = self::$driver->find_and_check_element( $settings_body_id );

		$this->assertNotNull( $settings_body );
	}
}
