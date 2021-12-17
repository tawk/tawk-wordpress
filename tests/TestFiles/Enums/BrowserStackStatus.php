<?php

namespace Tawk\Tests\TestFiles\Enums;

use ReflectionClass;

abstract class BrowserStackStatus {
	const PASSED = 'passed';
	const FAILED = 'failed';

	public static function is_valid_value( string $value ) {
		$o_class   = new ReflectionClass( __CLASS__ );
		$constants = $o_class->getConstants();
		return in_array( $value, $constants, true );
	}
}
