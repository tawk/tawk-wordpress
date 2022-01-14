<?php

/**
 * Base class for upgrades
 */
abstract class TawkToUpgradeBase {
	const VERSION = null;

	/**
	 * Gets current upgrade's release version
	 *
	 * @return string Release version
	 * @throws Exception Release version is not defined.
	 */
	public static function get_version() {
		if ( is_null( static::VERSION ) ) {
			throw new Exception( 'Subclass must have const VERSION' );
		}

		return static::VERSION;
	}

	/**
	 * Upgrade script
	 *
	 * @return void
	 * @throws Exception Subclass has no upgrade implementation.
	 */
	public static function upgrade() {
		throw new Exception( 'Subclass must implement this!' );
	}
}
