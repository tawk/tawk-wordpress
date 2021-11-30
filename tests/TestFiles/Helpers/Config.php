<?php

namespace Tawk\Tests\TestFiles\Helpers;

class Config {
	public static function get_config() {
		return array(
			'browser' => getenv('BROWSER'),
			'property_id' => getenv('PROPERTY_ID'),
			'widget_id' => getenv('WIDGET_ID'),
			'tawk' => array(
				'user' => getenv('TAWK_USER'),
				'pass' => getenv('TAWK_PASS'),
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
					'host' => getenv('WEB_HOST'),
					'port' => getenv('WEB_PORT'),
				),
				'selenium' => array(
					'host' => getenv('SELENIUM_HOST'),
					'port' => getenv('SELENIUM_PORT'),
				)
			)
		);
	}
}
