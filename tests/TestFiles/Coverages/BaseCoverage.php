<?php

namespace Tawk\Tests\TestFiles\Coverages;

use PHPUnit\Framework\TestCase;

abstract class BaseCoverage extends TestCase {
	const BASE_TEST_URL = 'http://wordpress/'; // TODO: this needs to be shared across from a common file
	protected $driver;

	public function create_driver(): void {
		throw new Exception('Subclass should implement this');
	}
}
