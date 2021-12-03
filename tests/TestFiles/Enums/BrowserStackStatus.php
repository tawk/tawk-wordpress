<?php

namespace Tawk\Tests\TestFiles\Enums;

use ReflectionClass;

abstract class BrowserStackStatus {
	const PASSED = 'passed';
	const FAILED = 'failed';

	public static function isValidValue( string $value ) {
		$oClass = new ReflectionClass( __CLASS__ );
		$constants = $oClass->getConstants();
		return in_array( $value, $constants );
	}
}
