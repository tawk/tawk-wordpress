<?php

namespace Tawk\Tests\TestFiles\Helpers;

class Common {
	public static function build_url( $host, $port=null ) {
		if ( false === isset( $port ) ) {
			return 'http://'.$host.'/';
		}

		return 'http://'.$host.':'.$port.'/';
	}

	public static function get_env( $env_var_name ) {
		$env_var = getenv( $env_var_name );

		if ( false === $env_var ) {
			return null;
		}

		return $env_var;
	}
}
