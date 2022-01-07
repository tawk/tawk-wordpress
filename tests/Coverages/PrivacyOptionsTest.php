<?php

namespace Tawk\Tests\Coverages;

use PHPUnit\Framework\TestCase;

use Tawk\Tests\TestFiles\Config;
use Tawk\Tests\TestFiles\Helpers\Common;
use Tawk\Tests\TestFiles\Modules\Web;
use Tawk\Tests\TestFiles\Modules\Webdriver;

class PrivacyOptionsTest extends TestCase {
	protected static Webdriver $driver;
	protected static Web $web;
	protected static string $script_selector;
	protected static string $admin_name;
	protected static string $admin_email;

	public static function setupBeforeClass(): void {
		$config = Config::get_config();

		self::$driver = Common::create_driver( $config );
		self::$web    = Common::create_web( self::$driver, $config );

		self::$script_selector = '#tawk-script';

		self::$admin_name  = $config->web->admin->name;
		self::$admin_email = $config->web->admin->email;

		self::$web->login();

		self::$web->install_plugin();
		self::$web->activate_plugin();
		self::$web->set_widget( $config->tawk->property_id, $config->tawk->widget_id );
	}

	public function setup(): void {
		self::$web->login();
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
