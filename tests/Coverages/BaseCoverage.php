<?php

namespace Tawk\Tests\Coverages;

use PHPUnit\Framework\TestCase;

use Tawk\Tests\TestFiles\Helpers\Common;
use Tawk\Tests\TestFiles\Helpers\Config;
use Tawk\Tests\TestFiles\Helpers\Webdriver;
use Tawk\Tests\TestFiles\Helpers\Web;

abstract class BaseCoverage extends TestCase {
	protected $config;
	protected $driver;
	protected $web;

	public function setup(): void {
		$this->config = Config::get_config();

		$urls = $this->config['urls'];
		$selenium_port = isset( $urls['selenium']['port'] ) ? $urls['selenium']['port'] : 4444;
		$selenium_url = Common::build_url( $urls['selenium']['host'], $selenium_port );

		$this->driver = Webdriver::create_driver( $this->config['browser'], $selenium_url );

		$this->web = new Web( $this->driver, array(
			'base_test_url' => $this->config['base_test_url'],
			'admin' => $this->config['admin'],
			'tawk' => $this->config['tawk'],
			'web' => $urls['web'],
		) );
	}
}
