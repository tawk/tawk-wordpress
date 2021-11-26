<?php

namespace Tawk\Tests\TestFiles\Coverages;

use Facebook\WebDriver\WebDriverBy;

use Tawk\Tests\TestFiles\Helpers\Web;

abstract class WidgetSet extends BaseCoverage {
	public function setup(): void {
		$this->create_driver();

		Web::login( $this->driver );

		$this->assertEquals( $this::BASE_TEST_URL.'wp-admin/', $this->driver->getCurrentURL() );

		Web::install_plugin( $this->driver );
		Web::activate_plugin( $this->driver );
	}

	public function tearDown(): void {
		try {
			Web::deactivate_plugin( $this->driver );
			Web::uninstall_plugin( $this->driver );
		} catch (Exception $e) {
			// Do nothing
		}

		$this->driver->quit();
	}

	/**
	 * @test
	 * @runInSeparateProcess
	 */
	public function should_set_widget(): void {
		$property_id = '6045e421385de407571da88d'; // TODO: move this to config
		$widget_id = '1f08g81vl'; // TODO: move this to config

		Web::set_widget( $this->driver, $property_id, $widget_id );

		Web::goto_widget_selection( $this->driver );

		$property_field = $this->driver->findElement( WebDriverBy::id( 'property' ) );
		$selected_property = $property_field->findElement( WebDriverBy::cssSelector( 'li.change-item.active' ) )->getAttribute('data-id');
		$this->assertEquals($selected_property, $property_id);

		$widget_field = $this->driver->findElement( WebDriverBy::id( 'widget-'.$property_id ) );
		$selected_widget = $widget_field->findElement( WebDriverBy::cssSelector( 'li.change-item.active' ) )->getAttribute('data-id');
		$this->assertEquals($selected_widget, $widget_id);
	}
}
