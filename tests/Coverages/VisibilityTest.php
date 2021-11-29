<?php

namespace Tawk\Tests\Coverages;

use Facebook\WebDriver\WebDriverBy;

use Tawk\Tests\TestFiles\Helpers\Webdriver;

class VisibilityTest extends BaseCoverage {
	protected $property_id;
	protected $widget_id;

	public function setup(): void {
		parent::setup();

		$this->frontend_driver = Webdriver::create_driver( $this->config['browser'] );

		$this->web->login();

		$this->assertEquals( $this->web->get_admin_url(), $this->driver->getCurrentURL() );

		$this->web->install_plugin();
		$this->web->activate_plugin();

		$this->property_id = $this->config['property_id'];
		$this->widget_id = $this->config['widget_id'];
		$this->web->set_widget( $this->property_id, $this->widget_id );
	}

	public function tearDown(): void {
		try {
			$this->web->deactivate_plugin();
			$this->web->uninstall_plugin();
		} catch (Exception $e) {
			// Do nothing
		}

		$this->driver->quit();
		$this->frontend_driver->quit();
	}

	/**
	 * @test
	 */
	public function should_show_widget_on_frontpage(): void {
		$this->frontend_driver->get( $this->web->get_base_test_url() );

		$embed_script = $this->config['base_tawk_embed_url'].$this->property_id.'/'.$this->widget_id;
		$script_els = $this->frontend_driver->findElements( WebDriverBy::cssSelector( 'script[src="'.$embed_script.'"]' ) );

		$this->assertEquals( 1, count( $script_els ) );
	}

	/**
	 * @test
	 */
	public function should_not_have_data_on_visitor_object_if_not_logged_in(): void {
		$this->frontend_driver->get( $this->web->get_base_test_url() );

		$visitor_data = $this->frontend_driver->executeScript('return Tawk_API.visitor');

		$this->assertEmpty( $visitor_data );
	}

	/**
	 * @test
	 */
	public function should_have_data_on_visitor_object_if_logged_in(): void {
		// admin user currently logged in
		$this->driver->get( $this->web->get_base_test_url() );

		$visitor_data = $this->driver->executeScript('return Tawk_API.visitor');

		$this->assertNotEmpty( $visitor_data );
		$this->assertEquals( $this->config['admin']['name'], $visitor_data[ 'name' ] );
		$this->assertEquals( $this->config['admin']['email'], $visitor_data[ 'email' ] );
	}
}
