<?php

namespace Tawk\Tests\Coverages;

use PHPUnit\Framework\TestCase;

use Tawk\Tests\TestFiles\Enums\BrowserStackStatus;
use Tawk\Tests\TestFiles\Config;
use Tawk\Tests\TestFiles\Helpers\Common;
use Tawk\Tests\TestFiles\Modules\Web;
use Tawk\Tests\TestFiles\Modules\Webdriver;

class WidgetSetTest extends TestCase {
	protected static Webdriver $driver;
	protected static Web $web;
	protected static string $property_id;
	protected static string $widget_id;

	public static function setupBeforeClass(): void {
		$config = Config::get_config();

		self::$driver = Common::create_driver( 'Widget Set Test', $config );
		self::$web = Common::create_web( self::$driver, $config );

		self::$property_id = $config->tawk->property_id;
		self::$widget_id = $config->tawk->widget_id;
	}

	public function setup(): void {
		self::$web->login();

		$this->assertEquals( self::$web->get_admin_url(), self::$driver->get_current_url() );

		self::$web->install_plugin();
		self::$web->activate_plugin();
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
	public function should_set_widget(): void {
		self::$web->set_widget( self::$property_id, self::$widget_id );

		self::$web->goto_widget_selection();

		$selected_property = self::$driver->find_element_and_get_attribute_value( '#property li.change-item.active', 'data-id' );
		$this->assertEquals($selected_property, self::$property_id);

		$selected_widget = self::$driver->find_element_and_get_attribute_value(
			'#widget-'.self::$property_id.' li.change-item.active',
			'data-id'
		);
		$this->assertEquals($selected_widget, self::$widget_id);
	}
}
