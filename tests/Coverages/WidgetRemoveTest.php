<?php

namespace Tawk\Tests\Coverages;

use PHPUnit\Framework\TestCase;

use Tawk\Tests\TestFiles\Enums\BrowserStackStatus;
use Tawk\Tests\TestFiles\Config;
use Tawk\Tests\TestFiles\Helpers\Common;
use Tawk\Tests\TestFiles\Modules\Web;
use Tawk\Tests\TestFiles\Modules\Webdriver;

class WidgetRemoveTest extends TestCase {
	protected static Webdriver $driver;
	protected static Web $web;
	protected static string $property_id;
	protected static string $widget_id;

	public static function setupBeforeClass(): void {
		$config = Config::get_config();

		self::$driver = Common::create_driver( 'Widget Remove Test', $config );
		self::$web = Common::create_web( self::$driver, $config );

		self::$property_id = $config->tawk->property_id;
		self::$widget_id = $config->tawk->widget_id;
	}

	public function setup(): void {
		self::$web->login();

		$this->assertEquals( self::$web->get_admin_url(), self::$driver->get_current_url() );

		self::$web->install_plugin();
		self::$web->activate_plugin();

		self::$web->set_widget( self::$property_id, self::$widget_id );
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
	public function should_remove_widget(): void {
		self::$web->remove_widget();

		self::$web->goto_widget_selection();

		$selected_property = self::$driver->find_and_check_element( '#property li.change-item.active' );
		$this->assertNull( $selected_property );
	}
}
