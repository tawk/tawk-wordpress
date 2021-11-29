<?php

namespace Tawk\Tests\Coverages;

use Facebook\WebDriver\WebDriverBy;

class VisibilityTest extends BaseCoverage {
	protected $property_id;
	protected $widget_id;

	public function setup(): void {
		parent::setup();
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
	}

	/**
	 * @test
	 */
	public function should_show_widget_on_frontpage(): void {
		$this->driver->get( $this->web->get_base_test_url() );

		$embed_script = $this->config['base_tawk_embed_url'].$this->property_id.'/'.$this->widget_id;
		$script_els = $this->driver->findElements( WebDriverBy::cssSelector( 'script[src="'.$embed_script.'"]' ) );

		$this->assertEquals( 1, count( $script_els ) );
	}
}
