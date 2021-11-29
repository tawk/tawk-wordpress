<?php

namespace Tawk\Tests\Coverages;

use PHPUnit\Framework\TestCase;

use Tawk\Tests\TestFiles\Helpers\Config;
use Tawk\Tests\TestFiles\Helpers\Webdriver;
use Tawk\Tests\TestFiles\Helpers\Web;

abstract class BaseCoverage extends TestCase {
	protected $config;
	protected $driver;
	protected $web;

	public function setup(): void {
		$this->config = Config::get_config();

		$this->driver = Webdriver::create_driver( $this->config['browser'] );

		$this->web = new Web( $this->driver, array(
			'base_test_url' => $this->config['base_test_url'],
			'admin' => $this->config['admin'],
			'tawk' => $this->config['tawk'],
		) );
	}
}
