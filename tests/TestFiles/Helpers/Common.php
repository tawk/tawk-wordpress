<?php

namespace Tawk\Tests\TestFiles\Helpers;

class Common {
	public static function build_url( $host, $port=null ) {
		if ( false === is_null( $port ) ) {
			return 'http://'.$host.'/';
		}

		return 'http://'.$host.':'.$port.'/';
	}
}
