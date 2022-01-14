<?php

if ( ! class_exists( 'TawkToUpgradeBase' ) ) {
	require_once dirname( __FILE__ ) . '/base.php';
}

use Tawk\Helpers\PathHelper;
use Tawk\Helpers\Common as CommonHelper;

/**
 * Upgrade for release version 0.7.0
 */
class TawkToUpgradeVersion070 extends TawkToUpgradeBase {
	const VERSION = '0.7.0';

	/**
	 * Migration for url patterns with ending wildcards. (ex. https://www.example.com/path/to/somewhere/*)
	 *
	 * Adds the same url pattern without the ending wildcard (ex. https://www.example.com/path/to/somewhere)
	 * to adjust with the new pattern matching lib.
	 */
	public static function upgrade() {
		$visibility = get_option( TawkTo_Settings::TAWK_VISIBILITY_OPTIONS );

		$visibility['included_url_list'] = self::process_patterns( $visibility['included_url_list'] );
		$visibility['excluded_url_list'] = self::process_patterns( $visibility['excluded_url_list'] );

		update_option( TawkTo_Settings::TAWK_VISIBILITY_OPTIONS, $visibility );
	}

	/**
	 * Processes the patterns with ending wildcards and adds
	 * a copy of it without the wildcard to the list.
	 *
	 * @param string $pattern_list Comma separated pattern list.
	 *
	 * @return string Updated pattern list.
	 */
	protected static function process_patterns( $pattern_list ) {
		$splitted_pattern_list = array_map( 'trim', preg_split( '/,/', $pattern_list ) );
		$wildcard              = PathHelper::get_wildcard();

		$new_pattern_list = array();
		$added_patterns   = array();

		foreach ( $splitted_pattern_list as $url ) {
			if ( empty( $url ) ) {
				continue;
			}

			$new_pattern_list[] = $url;

			if ( false === CommonHelper::text_ends_with( $url, $wildcard ) ) {
				continue;
			}

			$new_pattern = rtrim( $url, '/' . $wildcard );
			if ( in_array( $new_pattern, $splitted_pattern_list, true ) ) {
				continue;
			}

			if ( true === isset( $added_patterns[ $new_pattern ] ) ) {
				continue;
			}

			$new_pattern_list[]             = $new_pattern;
			$added_patterns[ $new_pattern ] = true;
		}

		return join( ', ', $new_pattern_list );
	}
}
