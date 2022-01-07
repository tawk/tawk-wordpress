<?php

namespace Tawk\Tests\Coverages;

use PHPUnit\Framework\TestCase;

use Tawk\Tests\TestFiles\Config;
use Tawk\Tests\TestFiles\Helpers\Common;
use Tawk\Tests\TestFiles\Modules\Web;
use Tawk\Tests\TestFiles\Modules\Webdriver;

/**
 * @testdox Widget Selection Test
 */
class WidgetSelectionTest extends TestCase {
	protected static Webdriver $driver;
	protected static Web $web;
	protected static string $property_id;
	protected static string $widget_id;

	public static function setUpBeforeClass(): void {
		$config = Config::get_config();

		self::$driver = Common::create_driver( $config );
		self::$web    = Common::create_web( self::$driver, $config );

		self::$property_id = $config->tawk->property_id;
		self::$widget_id   = $config->tawk->widget_id;

		self::$web->login();

		self::$web->install_plugin();
		self::$web->activate_plugin();
	}

	public static function tearDownAfterClass(): void {
		self::$web->login();
		self::$web->deactivate_plugin();
		self::$web->uninstall_plugin();

		self::$driver->quit();
	}

	/**
	 * @test
	 * @group widget_selection_test
	 */
	public function should_be_able_to_set_and_remove_widget() {
		$script_selector = '#tawk-script';

		self::$web->set_widget( self::$property_id, self::$widget_id );

		self::$web->logout();
		self::$driver->goto_page( self::$web->get_base_url() );

		$script = self::$driver->find_and_check_element( $script_selector );

		$this->assertNotNull( $script );

		self::$web->login();
		self::$web->remove_widget();

		self::$web->logout();
		self::$driver->goto_page( self::$web->get_base_url() );

		$script = self::$driver->find_and_check_element( $script_selector );

		$this->assertNull( $script );
	}
}
