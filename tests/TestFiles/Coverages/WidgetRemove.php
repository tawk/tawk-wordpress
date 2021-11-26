<?php

namespace Tawk\Tests\TestFiles\Coverages;

use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;

use Tawk\Tests\TestFiles\Helpers\Web;

abstract class WidgetRemove extends BaseCoverage {
	public function setup(): void {
		$this->create_driver();

		Web::login( $this->driver );

		$this->assertEquals( $this::BASE_TEST_URL.'wp-admin/', $this->driver->getCurrentURL() );

		Web::install_plugin( $this->driver );
		Web::activate_plugin( $this->driver );

		$property_id = '6045e421385de407571da88d'; // TODO: move this to config
		$widget_id = '1f08g81vl'; // TODO: move this to config

		Web::set_widget( $this->driver, $property_id, $widget_id );
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
	public function should_remove_widget(): void {
		Web::remove_widget( $this->driver );

		Web::goto_widget_selection( $this->driver );

		$property_field = $this->driver->findElement( WebDriverBy::id( 'property' ) );
		$selected_property = $property_field->findElements( WebDriverBy::cssSelector( 'li.change-item.active' ) );
		$this->assertTrue( 0 === count( $selected_property ) );
	}
}
