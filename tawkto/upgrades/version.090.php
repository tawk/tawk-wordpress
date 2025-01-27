<?php

if ( ! class_exists( 'TawkToUpgradeBase' ) ) {
	require_once dirname( __FILE__ ) . '/base.php';
}

/**
 * Upgrade for release version 0.9.0
 */
class TawkToUpgradeVersion090 extends TawkToUpgradeBase {
	const VERSION = '0.9.0';

	/**
	 * Migration for visitor recognition
	 *
	 * Move the visitor recognition setting from visibility to privacy.
	 */
	public static function upgrade() {
		$visibility = get_option( TawkTo_Settings::TAWK_VISIBILITY_OPTIONS, array() );

		if ( isset( $visibility['enable_visitor_recognition'] ) ) {
			update_option( TawkTo_Settings::TAWK_PRIVACY_OPTIONS, array( 'enable_visitor_recognition' => $visibility['enable_visitor_recognition'] ) );

			unset( $visibility['enable_visitor_recognition'] );
			update_option( TawkTo_Settings::TAWK_VISIBILITY_OPTIONS, $visibility );
		}

		update_option( TawkTo_Settings::TAWK_SECURITY_OPTIONS, array( 'js_api_key' => '' ) );
	}
}
