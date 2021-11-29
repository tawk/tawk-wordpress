<?php

namespace Tawk\Tests\Coverages;

use Facebook\WebDriver\WebDriverBy;

class WidgetSetTest extends BaseCoverage {
	public function setup(): void {
		parent::setup();
		$this->web->login();

		$this->assertEquals( $this->web->get_admin_url(), $this->driver->getCurrentURL() );

		$this->web->install_plugin();
		$this->web->activate_plugin();
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
	public function should_set_widget(): void {
		$property_id = $this->config['property_id'];
		$widget_id = $this->config['widget_id'];

		$this->web->set_widget( $property_id, $widget_id );

		$this->web->goto_widget_selection();

		$property_field = $this->driver->findElement( WebDriverBy::id( 'property' ) );
		$selected_property = $property_field->findElement( WebDriverBy::cssSelector( 'li.change-item.active' ) )->getAttribute('data-id');
		$this->assertEquals($selected_property, $property_id);

		$widget_field = $this->driver->findElement( WebDriverBy::id( 'widget-'.$property_id ) );
		$selected_widget = $widget_field->findElement( WebDriverBy::cssSelector( 'li.change-item.active' ) )->getAttribute('data-id');
		$this->assertEquals($selected_widget, $widget_id);
	}
}
