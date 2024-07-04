<?php

namespace Tawk\Tests\Coverages;

use PHPUnit\Framework\TestCase;

use Tawk\Tests\TestFiles\Config;
use Tawk\Tests\TestFiles\Helpers\Common;
use Tawk\Tests\TestFiles\Modules\Web;
use Tawk\Tests\TestFiles\Modules\Webdriver;

class WoocommerceOptionsTest extends TestCase {
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

		self::$web->activate_plugin();
		self::$web->set_widget( $config->tawk->property_id, $config->tawk->widget_id );
	}

	public function setup(): void {
		self::$web->login();

		self::$web->goto_visibility_options();
		self::$web->toggle_switch( '#always-display', false );

		self::$web->goto_woocommerce_options();
	}

	public function teardown(): void {
		self::$web->login();
		self::$web->reset_woocommerce_options( false );
		self::$web->reset_visibility_options();
	}

	public static function tearDownAfterClass(): void {
		self::$web->login();
		self::$web->deactivate_plugin();
		self::$web->uninstall_plugin();

		self::$driver->quit();
	}

	private function check_widget_on_page( $url ) {
		self::$web->logout();
		self::$driver->goto_page( $url );

		$script = self::$driver->find_and_check_element( self::$script_selector );

		$this->assertNotNull( $script );
	}

	private function check_widget_not_on_page( $url ) {
		self::$web->logout();
		self::$driver->goto_page( $url );

		$script = self::$driver->find_and_check_element( self::$script_selector );

		$this->assertNull( $script );
	}

	/**
	 * @test
	 * @group woocommerce_opts_shop_main_page
	 */
	public function should_display_widget_on_shop_main_page_if_option_is_enabled() {
		self::$web->toggle_switch( '#display-on-shop', true );

		self::$driver->move_mouse_to( '#submit-header' )->click();
		self::$driver->wait_for_seconds( 1 );

		$this->check_widget_on_page( self::$web->get_base_url() . 'shop/' );
	}

	/**
	 * @test
	 * @group woocommerce_opts_shop_main_page
	 */
	public function should_display_widget_on_shop_main_page_if_option_is_disabled_and_always_display_is_enabled() {
		self::$web->toggle_switch( '#display-on-shop', false );

		self::$web->goto_visibility_options();
		self::$web->toggle_switch( '#always-display', true );

		self::$driver->move_mouse_to( '#submit-header' )->click();
		self::$driver->wait_for_seconds( 1 );

		$this->check_widget_on_page( self::$web->get_base_url() . 'shop/' );
	}

	/**
	 * @test
	 * @group woocommerce_opts_shop_main_page
	 */
	public function should_not_display_widget_on_shop_main_page_if_option_is_disabled() {
		self::$web->toggle_switch( '#display-on-shop', false );

		self::$driver->move_mouse_to( '#submit-header' )->click();
		self::$driver->wait_for_seconds( 1 );

		$this->check_widget_not_on_page( self::$web->get_base_url() . 'shop/' );
	}

	/**
	 * @test
	 * @group woocommerce_opts_product_category
	 */
	public function should_display_widget_on_product_category_if_option_is_enabled() {
		self::$web->toggle_switch( '#display-on-productcategory', true );

		self::$driver->move_mouse_to( '#submit-header' )->click();
		self::$driver->wait_for_seconds( 1 );

		$this->check_widget_on_page( self::$web->get_base_url() . 'product-category/music/' );
	}

	/**
	 * @test
	 * @group woocommerce_opts_product_category
	 */
	public function should_display_widget_on_product_category_if_option_is_disabled_and_always_display_is_enabled() {
		self::$web->toggle_switch( '#display-on-productcategory', false );

		self::$web->goto_visibility_options();
		self::$web->toggle_switch( '#always-display', true );

		self::$driver->move_mouse_to( '#submit-header' )->click();
		self::$driver->wait_for_seconds( 1 );

		$this->check_widget_on_page( self::$web->get_base_url() . 'product-category/music/' );
	}

	/**
	 * @test
	 * @group woocommerce_opts_product_category
	 */
	public function should_not_display_widget_on_product_category_if_option_is_disabled() {
		self::$web->toggle_switch( '#display-on-productcategory', false );

		self::$driver->move_mouse_to( '#submit-header' )->click();
		self::$driver->wait_for_seconds( 1 );

		$this->check_widget_not_on_page( self::$web->get_base_url() . 'product-category/music/' );
	}

	/**
	 * @test
	 * @group woocommerce_opts_product_page
	 */
	public function should_display_widget_on_product_page_if_option_is_enabled() {
		self::$web->toggle_switch( '#display-on-productpage', true );

		self::$driver->move_mouse_to( '#submit-header' )->click();
		self::$driver->wait_for_seconds( 1 );

		$this->check_widget_on_page( self::$web->get_base_url() . 'product/album/' );
	}

	/**
	 * @test
	 * @group woocommerce_opts_product_page
	 */
	public function should_display_widget_on_product_page_if_option_is_disabled_and_always_display_is_enabled() {
		self::$web->toggle_switch( '#display-on-productpage', false );

		self::$web->goto_visibility_options();
		self::$web->toggle_switch( '#always-display', true );

		self::$driver->move_mouse_to( '#submit-header' )->click();
		self::$driver->wait_for_seconds( 1 );

		$this->check_widget_on_page( self::$web->get_base_url() . 'product/album/' );
	}

	/**
	 * @test
	 * @group woocommerce_opts_product_page
	 */
	public function should_not_display_widget_on_product_page_if_option_is_disabled() {
		self::$web->toggle_switch( '#display-on-productpage', false );

		self::$driver->move_mouse_to( '#submit-header' )->click();
		self::$driver->wait_for_seconds( 1 );

		$this->check_widget_not_on_page( self::$web->get_base_url() . 'product/album/' );
	}

	/**
	 * @test
	 * @group woocommerce_opts_product_tag
	 */
	public function should_display_widget_on_product_tag_if_option_is_enabled() {
		self::$web->toggle_switch( '#display-on-producttag', true );

		self::$driver->move_mouse_to( '#submit-header' )->click();
		self::$driver->wait_for_seconds( 1 );

		$this->check_widget_on_page( self::$web->get_base_url() . 'product-tag/product-tag-a/' );
	}

	/**
	 * @test
	 * @group woocommerce_opts_product_tag
	 */
	public function should_display_widget_on_product_tag_if_option_is_disabled_and_always_display_is_enabled() {
		self::$web->toggle_switch( '#display-on-producttag', false );

		self::$web->goto_visibility_options();
		self::$web->toggle_switch( '#always-display', true );

		self::$driver->move_mouse_to( '#submit-header' )->click();
		self::$driver->wait_for_seconds( 1 );

		$this->check_widget_on_page( self::$web->get_base_url() . 'product-tag/product-tag-a/' );
	}

	/**
	 * @test
	 * @group woocommerce_opts_product_tag
	 */
	public function should_not_display_widget_on_product_tag_if_option_is_disabled() {
		self::$web->toggle_switch( '#display-on-producttag', false );

		self::$driver->move_mouse_to( '#submit-header' )->click();
		self::$driver->wait_for_seconds( 1 );

		$this->check_widget_not_on_page( self::$web->get_base_url() . 'product-tag/product-tag-a/' );
	}
}
