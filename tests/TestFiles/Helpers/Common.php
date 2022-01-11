<?php

namespace Tawk\Tests\TestFiles\Helpers;

use Tawk\Tests\TestFiles\Modules\Web;
use Tawk\Tests\TestFiles\Modules\Webdriver;
use Tawk\Tests\TestFiles\Types\Config;
use Tawk\Tests\TestFiles\Types\UrlConfig;
use Tawk\Tests\TestFiles\Types\Web\WebConfiguration;
use Tawk\Tests\TestFiles\Types\Web\WebDependencies;
use Tawk\Tests\TestFiles\Types\Webdriver\WebdriverConfig;

class Common {
	public static function build_url( UrlConfig $url_config ): string {
		$protocol = $url_config->https_flag ? 'https' : 'http';
		$host     = $url_config->host;
		$port     = $url_config->port;

		if ( true === empty( $port ) ) {
			return $protocol . '://' . $host . '/';
		}

		return $protocol . '://' . $host . ':' . $port . '/';
	}

	public static function build_selenium_url(
		UrlConfig $url_config,
		bool $is_hub = false
	): string {
		$url = self::build_url( $url_config );

		if ( false === $is_hub ) {
			return $url;
		}

		return $url . 'wd/hub';
	}

	public static function get_env( string $env_var_name ): string {
		$env_var = getenv( $env_var_name );

		if ( false === $env_var ) {
			return '';
		}

		return $env_var;
	}

	public static function create_driver( Config $config ): Webdriver {
		$webdriver_config = new WebdriverConfig();

		$webdriver_config->selenium = $config->selenium;

		return new Webdriver( $webdriver_config );
	}

	public static function create_web( Webdriver $driver, Config $config ): Web {
		$web_dependencies = new WebDependencies();

		$web_dependencies->driver = $driver;

		$web_config = new WebConfiguration();

		$web_config->tawk = $config->tawk;
		$web_config->web  = $config->web;

		return new Web( $web_dependencies, $web_config );
	}
}
