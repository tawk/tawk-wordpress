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
		$visibility            = get_option( TawkTo_Settings::TAWK_VISIBILITY_OPTIONS );
		$included_url_list     = array_map( 'trim', preg_split( '/,/', $visibility['included_url_list'] ) );
		$excluded_url_list     = array_map( 'trim', preg_split( '/,/', $visibility['excluded_url_list'] ) );
		$new_included_url_list = array();
		$new_excluded_url_list = array();

		foreach ( $included_url_list as $included_url ) {
			if ( empty( $included_url ) ) {
				continue;
			}

			$new_included_url_list[] = $included_url;

			$wildcard = PathHelper::get_wildcard();

			if ( false === CommonHelper::text_ends_with( $included_url, $wildcard ) ) {
				continue;
			}

			$new_included_url = rtrim( $included_url, '/' . $wildcard );
			if ( in_array( $new_included_url, $included_url_list, true ) ) {
				continue;
			}

			$new_included_url_list[] = $new_included_url;
		}

		foreach ( $excluded_url_list as $excluded_url ) {
			if ( empty( $excluded_url ) ) {
				continue;
			}

			$new_excluded_url_list[] = $excluded_url;

			$wildcard = PathHelper::get_wildcard();

			if ( false === CommonHelper::text_ends_with( $excluded_url, $wildcard ) ) {
				continue;
			}

			$new_excluded_url = rtrim( $excluded_url, '/' . $wildcard );
			if ( in_array( $new_excluded_url, $excluded_url_list, true ) ) {
				continue;
			}

			$new_excluded_url_list[] = $new_excluded_url;
		}

		$visibility['included_url_list'] = join( ', ', $new_included_url_list );
		$visibility['excluded_url_list'] = join( ', ', $new_excluded_url_list );

		update_option( TawkTo_Settings::TAWK_VISIBILITY_OPTIONS, $visibility );
	}
}
