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
			),
			'base_tawk_embed_url' => 'https://embed.tawk.to/',
		);
	}
}
