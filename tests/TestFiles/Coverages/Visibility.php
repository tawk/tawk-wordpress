<?php

namespace Tawk\Tests\TestFiles\Coverages;

use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;

use Tawk\Tests\TestFiles\Helpers\Web;

abstract class Visibility extends BaseCoverage {
	protected $property_id;
	protected $widget_id;

	public function setup(): void {
		$this->create_driver();

		Web::login( $this->driver );

		$this->assertEquals( $this::BASE_TEST_URL.'wp-admin/', $this->driver->getCurrentURL() );

		Web::install_plugin( $this->driver );
		Web::activate_plugin( $this->driver );

		$this->property_id = '6045e421385de407571da88d'; // TODO: move this to config
		$this->widget_id = '1f08g81vl'; // TODO: move this to config
		Web::set_widget( $this->driver, $this->property_id, $this->widget_id );
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
	public function should_show_widget_on_frontpage(): void {
		$this->driver->get( $this::BASE_TEST_URL );

		$embed_script = 'https://embed.tawk.to/'.$this->property_id.'/'.$this->widget_id;
		$script_els = $this->driver->findElements( WebDriverBy::cssSelector( 'script[src="'.$embed_script.'"]' ) );

		$this->assertEquals( 1, count( $script_els ) );
	}
}
