<?php

namespace Tawk\Tests\TestFiles;

use Tawk\Tests\TestFiles\Helpers\Common;
use Tawk\Tests\TestFiles\Types\BrowserStackConfig;
use Tawk\Tests\TestFiles\Types\Config as FullConfig;
use Tawk\Tests\TestFiles\Types\SeleniumConfig;
use Tawk\Tests\TestFiles\Types\TawkConfig;
use Tawk\Tests\TestFiles\Types\UrlConfig;
use Tawk\Tests\TestFiles\Types\WebConfig;
use Tawk\Tests\TestFiles\Types\WebUserConfig;

class Config {
	public static function get_config() {
		$config               = new FullConfig();
		$config->browserstack = self::get_browserstack_config();
		$config->selenium     = self::get_selenium_config();
		$config->tawk         = self::get_tawk_config();
		$config->web          = self::get_web_config();

		return $config;
	}

	private static function get_tawk_config(): TawkConfig {
		$config              = new TawkConfig();
		$config->username    = Common::get_env( 'TAWK_USERNAME' );
		$config->password    = Common::get_env( 'TAWK_PASSWORD' );
		$config->property_id = Common::get_env( 'TAWK_PROPERTY_ID' );
		$config->widget_id   = Common::get_env( 'TAWK_WIDGET_ID' );
		$config->embed_url   = 'https://embed.tawk.to/';

		return $config;
	}

	private static function get_selenium_config(): SeleniumConfig {
		$url             = new UrlConfig();
		$url->host       = Common::get_env( 'SELENIUM_HOST' );
		$url->port       = Common::get_env( 'SELENIUM_PORT' );
		$url->https_flag = 'true' === Common::get_env( 'SELENIUM_HTTPS_FLAG' );

		$config                     = new SeleniumConfig();
		$config->browser            = Common::get_env( 'SELENIUM_BROWSER' );
		$config->hub_flag           = 'true' === Common::get_env( 'SELENIUM_HUB_FLAG' );
		$config->url                = $url;
		$config->session_timeout_ms = 90 * 1000;
		$config->request_timeout_ms = 90 * 1000;

		return $config;
	}

	private static function get_browserstack_config(): BrowserStackConfig {
		$config                   = new BrowserStackConfig();
		$config->username         = Common::get_env( 'BROWSERSTACK_USERNAME' );
		$config->access_key       = Common::get_env( 'BROWSERSTACK_ACCESS_KEY' );
		$config->local_identifier = Common::get_env( 'BROWSERSTACK_LOCAL_IDENTIFIER' );
		$config->build_name       = Common::get_env( 'BROWSERSTACK_BUILD_NAME' );
		$config->project_name     = Common::get_env( 'BROWSERSTACK_PROJECT_NAME' );
		$config->is_browserstack  = false === empty( $config->username );

		return $config;
	}

	private static function get_web_config(): WebConfig {
		$url       = new UrlConfig();
		$url->host = Common::get_env( 'WEB_HOST' );
		$url->port = Common::get_env( 'WEB_PORT' );

		$admin           = new WebUserConfig();
		$admin->username = 'admin';
		$admin->password = 'admin';
		$admin->name     = 'admin';
		$admin->email    = 'admin@example.com';

		$config        = new WebConfig();
		$config->url   = $url;
		$config->admin = $admin;

		return $config;
	}
}
