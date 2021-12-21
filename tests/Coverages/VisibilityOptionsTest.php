<?php

namespace Tawk\Tests\Coverages;

use PHPUnit\Framework\TestCase;
use Tawk\Tests\TestFiles\Config;
use Tawk\Tests\TestFiles\Enums\BrowserStackStatus;
use Tawk\Tests\TestFiles\Helpers\Common;
use Tawk\Tests\TestFiles\Modules\Web;
use Tawk\Tests\TestFiles\Modules\Webdriver;

/**
 * @testdox Visibility Options Test
 */
class VisibilityOptionsTest extends TestCase {
	protected static Webdriver $driver;
	protected static Web $web;
	protected static string $property_id;
	protected static string $widget_id;
	protected static string $script_selector;

	public static function setUpBeforeClass(): void {
		$config = Config::get_config();

		self::$driver = Common::create_driver( 'Visibility Options Test', $config );
		self::$web    = Common::create_web( self::$driver, $config );

		self::$property_id = $config->tawk->property_id;
		self::$widget_id   = $config->tawk->widget_id;

		$embed_script_url      = $config->tawk->embed_url . self::$property_id . '/' . self::$widget_id;
		self::$script_selector = 'script[src="' . $embed_script_url . '"]';

		self::$web->login();

		self::assertEquals( self::$web->get_admin_url(), self::$driver->get_current_url() );

		self::$web->install_plugin();
		self::$web->activate_plugin();
		self::$web->set_widget( self::$property_id, self::$widget_id );
	}

	public function setup(): void {
		self::$web->login();
		self::$web->goto_visibility_options();
	}

	protected function onNotSuccessfulTest( $err ): void {
		self::$driver->update_test_status( BrowserStackStatus::FAILED, $err->getMessage() );
		throw $err;
	}

	public function teardown(): void {
		self::$web->login();
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
	 * @group visibility_opts_always_display_enabled_exclude_url
	 */
	public function should_not_display_widget_on_excluded_page_while_always_display_is_enabled() {
		$excluded_url = self::$web->get_base_url() . 'category/category-a/';
		self::$web->toggle_switch( '#exclude-url', true );
		self::$driver->find_element_and_input( '#excluded-url-list', $excluded_url );

		self::$driver->move_mouse_to( '#submit-header' )->click();

		$this->check_widget_not_on_page( $excluded_url );
	}

	/**
	 * @test
	 * @group visibility_opts_always_display_enabled_exclude_url
	 */
	public function should_not_display_widget_on_excluded_pages_match_by_wildcard_while_always_display_is_enabled() {
		$excluded_url = self::$web->get_base_url() . 'category/*';
		self::$web->toggle_switch( '#exclude-url', true );
		self::$driver->find_element_and_input( '#excluded-url-list', $excluded_url );

		self::$driver->move_mouse_to( '#submit-header' )->click();
		self::$driver->wait_until_page_fully_loads();

		$this->check_widget_not_on_page( self::$web->get_base_url() . 'category/' );
		$this->check_widget_not_on_page( self::$web->get_base_url() . 'category/category-a/' );
		$this->check_widget_not_on_page( self::$web->get_base_url() . 'category/category-b/' );
	}

	/**
	 * @test
	 * @group visibility_opts_always_display_enabled_exclude_url
	 */
	public function should_display_widget_on_non_excluded_pages_while_always_display_is_enabled() {
		$excluded_url = self::$web->get_base_url() . 'category/*';
		self::$web->toggle_switch( '#exclude-url', true );
		self::$driver->find_element_and_input( '#excluded-url-list', $excluded_url );

		self::$driver->move_mouse_to( '#submit-header' )->click();
		self::$driver->wait_until_page_fully_loads();

		$this->check_widget_on_page( self::$web->get_base_url() . 'tag/tag-a/' );
	}

	/**
	 * @test
	 * @group visibility_opts_always_display_disabled
	 */
	public function should_not_display_when_always_display_is_disabled() {
		self::$web->toggle_switch( '#always-display', false );
		self::$driver->move_mouse_to( '#submit-header' )->click();
		self::$driver->wait_until_page_fully_loads();

		$this->check_widget_not_on_page( self::$web->get_base_url() );
	}

	/**
	 * @test
	 * @group visibility_opts_always_display_disabled_include_url_enabled
	 */
	public function should_display_widget_on_included_page_while_always_display_is_disabled() {
		$included_url = self::$web->get_base_url() . 'category/category-a/';
		self::$web->toggle_switch( '#always-display', false );
		self::$web->toggle_switch( '#include-url', true );
		self::$driver->find_element_and_input( '#included-url-list', $included_url );

		self::$driver->move_mouse_to( '#submit-header' )->click();
		self::$driver->wait_until_page_fully_loads();

		$this->check_widget_on_page( $included_url );
	}

	/**
	 * @test
	 * @group visibility_opts_always_display_disabled_include_url_enabled
	 */
	public function should_display_widget_on_included_pages_matched_by_wildcard_while_always_display_is_disabled() {
		$included_url = self::$web->get_base_url() . 'category/*';
		self::$web->toggle_switch( '#always-display', false );
		self::$web->toggle_switch( '#include-url', true );
		self::$driver->find_element_and_input( '#included-url-list', $included_url );

		self::$driver->move_mouse_to( '#submit-header' )->click();
		self::$driver->wait_until_page_fully_loads();

		$this->check_widget_on_page( self::$web->get_base_url() . 'category/' );
		$this->check_widget_on_page( self::$web->get_base_url() . 'category/category-a/' );
		$this->check_widget_on_page( self::$web->get_base_url() . 'category/category-b/' );
	}

	/**
	 * @test
	 * @group visibility_opts_always_display_disabled_include_url_enabled
	 */
	public function should_display_widget_on_non_included_pages_while_always_display_is_disabled() {
		$included_url = self::$web->get_base_url() . 'category/*';
		self::$web->toggle_switch( '#always-display', false );
		self::$web->toggle_switch( '#include-url', true );
		self::$driver->find_element_and_input( '#included-url-list', $included_url );

		self::$driver->move_mouse_to( '#submit-header' )->click();
		self::$driver->wait_until_page_fully_loads();

		$this->check_widget_not_on_page( self::$web->get_base_url() . 'tag/tag-a/' );
	}

	/**
	 * @test
	 * @group visibility_opts_always_display_disabled_show_on_front_page_enabled
	 */
	public function should_display_widget_on_front_page_if_show_on_front_page_is_enabled_and_always_display_is_disabled() {
		self::$web->toggle_switch( '#always-display', false );
		self::$web->toggle_switch( '#show-onfrontpage', true );

		self::$driver->move_mouse_to( '#submit-header' )->click();
		self::$driver->wait_until_page_fully_loads();

		$this->check_widget_on_page( self::$web->get_base_url() );
	}

	/**
	 * @test
	 * @group visibility_opts_always_display_disabled_show_on_front_page_enabled
	 */
	public function should_not_display_widget_on_front_page_if_excluded() {
		self::$web->toggle_switch( '#always-display', false );
		self::$web->toggle_switch( '#show-onfrontpage', true );
		self::$web->toggle_switch( '#exclude-url', true );
		self::$driver->find_element_and_input( '#excluded-url-list', self::$web->get_base_url() );

		self::$driver->move_mouse_to( '#submit-header' )->click();
		self::$driver->wait_until_page_fully_loads();

		$this->check_widget_not_on_page( self::$web->get_base_url() );
	}

	/**
	 * @test
	 * @group visibility_opts_always_display_disabled_show_on_category_pages_enabled
	 */
	public function should_display_widget_on_category_pages_if_show_on_category_pages_is_enabled_and_always_display_is_disabled() {
		self::$web->toggle_switch( '#always-display', false );
		self::$web->toggle_switch( '#show-oncategory', true );

		self::$driver->move_mouse_to( '#submit-header' )->click();
		self::$driver->wait_until_page_fully_loads();

		$this->check_widget_on_page( self::$web->get_base_url() . 'category/category-a/' );
		$this->check_widget_on_page( self::$web->get_base_url() . 'category/category-b/' );
	}

	/**
	 * @test
	 * @group visibility_opts_always_display_disabled_show_on_category_pages_enabled
	 */
	public function should_not_display_widget_on_category_pages_if_excluded() {
		$category_a_url = self::$web->get_base_url() . 'category/category-a/';
		$category_b_url = self::$web->get_base_url() . 'category/category-b/';
		$excluded_urls  = $category_a_url . ', ' . $category_b_url;

		self::$web->toggle_switch( '#always-display', false );
		self::$web->toggle_switch( '#show-oncategory', true );
		self::$web->toggle_switch( '#exclude-url', true );
		self::$driver->find_element_and_input( '#excluded-url-list', $excluded_urls );

		self::$driver->move_mouse_to( '#submit-header' )->click();
		self::$driver->wait_until_page_fully_loads();

		$this->check_widget_not_on_page( $category_a_url );
		$this->check_widget_not_on_page( $category_b_url );
	}

	/**
	 * @test
	 * @group visibility_opts_always_display_disabled_show_on_tag_pages_enabled
	 */
	public function should_display_widget_on_tag_pages_if_show_on_tag_pages_is_enabled_and_always_display_is_disabled() {
		self::$web->toggle_switch( '#always-display', false );
		self::$web->toggle_switch( '#show-ontagpage', true );

		self::$driver->move_mouse_to( '#submit-header' )->click();
		self::$driver->wait_until_page_fully_loads();

		$this->check_widget_on_page( self::$web->get_base_url() . 'tag/tag-a/' );
		$this->check_widget_on_page( self::$web->get_base_url() . 'tag/tag-b/' );
	}

	/**
	 * @test
	 * @group visibility_opts_always_display_disabled_show_on_tag_pages_enabled
	 */
	public function should_not_display_widget_on_tag_pages_if_excluded() {
		$tag_a_url     = self::$web->get_base_url() . 'tag/tag-a/';
		$tag_b_url     = self::$web->get_base_url() . 'tag/tag-b/';
		$excluded_urls = $tag_a_url . ', ' . $tag_b_url;

		self::$web->toggle_switch( '#always-display', false );
		self::$web->toggle_switch( '#show-ontagpage', true );
		self::$web->toggle_switch( '#exclude-url', true );
		self::$driver->find_element_and_input( '#excluded-url-list', $excluded_urls );

		self::$driver->move_mouse_to( '#submit-header' )->click();
		self::$driver->wait_until_page_fully_loads();

		$this->check_widget_not_on_page( $tag_a_url );
		$this->check_widget_not_on_page( $tag_b_url );
	}

	/**
	 * @test
	 * @group visibility_opts_always_display_disabled_show_on_single_post_pages_enabled
	 */
	public function should_display_widget_on_single_post_pages_if_show_on_single_post_pages_is_enabled_and_always_display_is_disabled() {
		self::$web->toggle_switch( '#always-display', false );
		self::$web->toggle_switch( '#show-onarticlepages', true );

		self::$driver->move_mouse_to( '#submit-header' )->click();
		self::$driver->wait_until_page_fully_loads();

		$this->check_widget_on_page( self::$web->get_base_url() . 'hello-world/' );
	}

	/**
	 * @test
	 * @group visibility_opts_always_display_disabled_show_on_single_post_pages_enabled
	 */
	public function should_not_display_widget_on_single_post_pages_if_excluded() {
		$excluded_url = self::$web->get_base_url() . 'hello-world/';

		self::$web->toggle_switch( '#always-display', false );
		self::$web->toggle_switch( '#show-onarticlepages', true );
		self::$web->toggle_switch( '#exclude-url', true );
		self::$driver->find_element_and_input( '#excluded-url-list', $excluded_url );

		self::$driver->move_mouse_to( '#submit-header' )->click();
		self::$driver->wait_until_page_fully_loads();

		$this->check_widget_not_on_page( $excluded_url );
	}
}
