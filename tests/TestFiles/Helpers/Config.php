<?php

namespace Tawk\Tests\TestFiles\Helpers;

class Config {
	public static function get_config() {
		return array(
			'browser' => Common::get_env( 'BROWSER' ),
			'property_id' => Common::get_env( 'PROPERTY_ID' ),
			'widget_id' => Common::get_env( 'WIDGET_ID' ),
			'tawk' => array(
				'user' => Common::get_env( 'TAWK_USER' ),
				'pass' => Common::get_env( 'TAWK_PASS' ),
			),
			'admin' => array(
				'user' => 'admin',
				'pass' => 'admin',
				'name' => 'admin',
				'email' => 'admin@example.com',
			),
			'urls' => array(
				'embed' => 'https://embed.tawk.to/',
				'web' => array(
					'host' => Common::get_env( 'WEB_HOST' ),
					'port' => Common::get_env( 'WEB_PORT' ),
				),
				'selenium' => array(
					'host' => Common::get_env( 'SELENIUM_HOST' ),
					'port' => Common::get_env( 'SELENIUM_PORT' ),
				)
			)
		);
	}
}
