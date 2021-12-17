<?php

namespace Tawk\Tests\Coverages;

use PHPUnit\Framework\TestCase;

use Tawk\Tests\TestFiles\Enums\BrowserStackStatus;
use Tawk\Tests\TestFiles\Config;
use Tawk\Tests\TestFiles\Helpers\Common;
use Tawk\Tests\TestFiles\Modules\Web;
use Tawk\Tests\TestFiles\Modules\Webdriver;

class PrivacyOptionsTest extends TestCase {
	protected static Webdriver $driver;
	protected static Web $web;
	protected static string $property_id;
	protected static string $widget_id;
	protected static string $script_selector;
	protected static string $admin_name;
	protected static string $admin_email;

	public static function setupBeforeClass(): void {
		$config = Config::get_config();

		self::$driver = Common::create_driver( 'Privacy Options Test', $config );
		self::$web    = Common::create_web( self::$driver, $config );

		self::$property_id = $config->tawk->property_id;
		self::$widget_id   = $config->tawk->widget_id;

		$embed_script_url      = $config->tawk->embed_url . self::$property_id . '/' . self::$widget_id;
		self::$script_selector = 'script[src="' . $embed_script_url . '"]';

		self::$admin_name  = $config->web->admin->name;
		self::$admin_email = $config->web->admin->email;

		self::$web->login();

		self::assertEquals( self::$web->get_admin_url(), self::$driver->get_current_url() );

		self::$web->install_plugin();
		self::$web->activate_plugin();
		self::$web->set_widget( self::$property_id, self::$widget_id );
	}

	protected function onNotSuccessfulTest( $err ): void {
		self::$driver->update_test_status( BrowserStackStatus::FAILED, $err->getMessage() );
		throw $err;
	}

	public static function tearDownAfterClass(): void {
		self::$web->login();
		self::$web->deactivate_plugin();
		self::$web->uninstall_plugin();

		self::$driver->quit();
	}

	/**
	 * @test
	 * @group privacy_options
	 */
	public function should_have_data_on_visitor_object_if_logged_in(): void {
		// admin user currently logged in.
		self::$driver->goto_page( self::$web->get_base_url() );

		self::$driver->wait_until_element_is_located( self::$script_selector );

		$visitor_data = self::$driver->get_driver()->executeScript( 'return Tawk_API.visitor' );

		$this->assertNotEmpty( $visitor_data );
		$this->assertEquals( self::$admin_name, $visitor_data['name'] );
		$this->assertEquals( self::$admin_email, $visitor_data['email'] );
	}

	/**
	 * @test
	 * @group privacy_options
	 */
	public function should_not_have_data_on_visitor_object_if_not_logged_in(): void {
		self::$web->logout();
		self::$driver->goto_page( self::$web->get_base_url() );

		self::$driver->wait_until_element_is_located( self::$script_selector );

		$visitor_data = self::$driver->get_driver()->executeScript( 'return Tawk_API.visitor' );

		$this->assertEmpty( $visitor_data );
	}
}
