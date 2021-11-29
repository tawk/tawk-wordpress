<?php

namespace Tawk\Tests\Coverages;

use Facebook\WebDriver\WebDriverBy;

class WidgetRemoveTest extends BaseCoverage {
	public function setup(): void {
		parent::setup();
		$this->web->login();

		$this->assertEquals( $this->web->get_admin_url(), $this->driver->getCurrentURL() );

		$this->web->install_plugin();
		$this->web->activate_plugin();

		$this->web->set_widget( $this->config['property_id'], $this->config['widget_id'] );
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
	public function should_remove_widget(): void {
		$this->web->remove_widget();

		$this->web->goto_widget_selection();

		$property_field = $this->driver->findElement( WebDriverBy::id( 'property' ) );
		$selected_property = $property_field->findElements( WebDriverBy::cssSelector( 'li.change-item.active' ) );
		$this->assertTrue( 0 === count( $selected_property ) );
	}
}
