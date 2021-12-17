<?php
/**
 * @phpcs:disable Squiz.Commenting.FileComment
 * @phpcs:disable PHPCompatibility
 */

namespace Tawk\Tests\TestFiles\Modules;

use Facebook\WebDriver\Remote\LocalFileDetector;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;

use Tawk\Tests\TestFiles\Enums\BrowserStackStatus;
use Tawk\Tests\TestFiles\Helpers\Common;
use Tawk\Tests\TestFiles\Helpers\Webdriver as WebdriverHelper;
use Tawk\Tests\TestFiles\Types\BrowserStackConfig;
use Tawk\Tests\TestFiles\Types\SeleniumConfig;
use Tawk\Tests\TestFiles\Types\Webdriver\WebdriverConfig;

use Exception;

class Webdriver {
	protected RemoteWebDriver $driver;
	protected SeleniumConfig $selenium;
	protected BrowserStackConfig $browserstack;
	protected string $test_status;
	protected string $test_message;

	public function __construct( string $session_name, WebdriverConfig $config ) {
		$this->selenium     = $config->selenium;
		$this->browserstack = $config->browserstack;

		$selenium_url = Common::build_selenium_url(
			$this->selenium->url,
			$this->selenium->hub_flag
		);

		$capabilities = WebdriverHelper::build_capabilities(
			$this->selenium->browser,
			$session_name,
			$this->browserstack
		);

		$this->driver = RemoteWebDriver::create(
			$selenium_url,
			$capabilities,
			$this->selenium->session_timeout_ms,
			$this->selenium->request_timeout_ms
		);

		$this->test_status  = BrowserStackStatus::PASSED;
		$this->test_message = 'All test passed';
	}

	public function get_driver() {
		return $this->driver;
	}

	public function update_test_status( string $status, string $message ): void {
		if ( false === BrowserStackStatus::is_valid_value( $status ) ) {
			throw new Exception( 'Browserstack status is invalid' );
		}

		$this->test_status  = $status;
		$this->test_message = $message;
	}

	public function get_current_url(): string {
		return $this->driver->getCurrentURL();
	}

	public function goto_page( string $page_url ): void {
		if ( $page_url === $this->driver->getCurrentURL() ) {
			return;
		}

		$this->driver->get( $page_url );
		$this->wait_until_url_contains( $page_url );
	}

	public function find_element( string $selector ) {
		return $this->driver->findElement( WebDriverBy::cssSelector( $selector ) );
	}

	public function find_and_check_element( string $selector ) {
		try {
			return $this->find_element( $selector );
		} catch ( Exception $err ) {
			return null;
		}
	}

	public function find_element_and_click( string $selector ) {
		return $this->find_element( $selector )->click();
	}

	public function find_element_and_input( string $selector, string $input_value ) {
		return $this->find_element( $selector )->sendKeys( $input_value );
	}

	public function find_element_and_get_attribute_value( string $selector, string $attribute ) {
		return $this->find_element( $selector )->getAttribute( $attribute );
	}

	public function move_mouse_to( string $selector ) {
		$coordinate = $this->find_element( $selector )->getCoordinates();
		return $this->driver->getMouse()->mouseMove( $coordinate );
	}

	public function upload_file( string $selector, $file_path ) {
		return $this->find_element( $selector )
					->setFileDetector( new LocalFileDetector() )
					->sendKeys( $file_path );
	}

	public function switch_to_default_frame(): void {
		$this->driver->switchTo()->defaultContent();
	}

	public function wait_until_url_contains(
		string $url_to_compare,
		int $wait_sec = 10,
		int $interval_ms = 500
	) {
		return $this->driver->wait( $wait_sec, $interval_ms )->until(
			WebDriverExpectedCondition::urlContains( $url_to_compare )
		);
	}

	public function wait_until_element_is_located(
		string $selector,
		int $wait_sec = 10,
		int $interval_ms = 500
	) {
		return $this->driver->wait( $wait_sec, $interval_ms )->until(
			WebDriverExpectedCondition::presenceOfElementLocated(
				WebDriverBy::cssSelector( $selector )
			)
		);
	}

	public function wait_until_element_text_contains(
		string $selector,
		string $text_to_compare,
		int $wait_sec = 10,
		int $interval_ms = 500
	) {
		return $this->driver->wait( $wait_sec, $interval_ms )->until(
			WebDriverExpectedCondition::elementTextContains(
				WebDriverBy::cssSelector( $selector ),
				$text_to_compare
			)
		);
	}

	public function wait_until_element_is_clickable(
		string $selector,
		int $wait_sec = 10,
		int $interval_ms = 500
	) {
		return $this->driver->wait( $wait_sec, $interval_ms )->until(
			WebDriverExpectedCondition::elementToBeClickable(
				WebDriverBy::cssSelector( $selector )
			)
		);
	}

	public function wait_for_alert_and_accept(): void {
		$this->driver->wait()->until( WebDriverExpectedCondition::alertIsPresent() );
		$this->driver->switchTo()->alert()->accept();
		$this->switch_to_default_frame();
	}

	public function wait_for_frame_and_switch(
		string $selector,
		int $wait_sec = 10,
		int $interval_ms = 500
	): void {
		$this->driver->wait( $wait_sec, $interval_ms )->until(
			WebDriverExpectedCondition::frameToBeAvailableAndSwitchToIt(
				$this->find_element( $selector )
			)
		);
	}

	public function clear_input( $selector ): void {
		$this->find_element( $selector )->clear();
	}

	public function quit(): void {
		if ( true === $this->browserstack->is_browserstack ) {
			$this->driver->executeScript( 'browserstack_executor: {"action": "setSessionStatus", "arguments": {"status":"' . $this->test_status . '", "reason": "' . $this->test_message . '"}}' );
		}

		$this->driver->quit();
	}
}
