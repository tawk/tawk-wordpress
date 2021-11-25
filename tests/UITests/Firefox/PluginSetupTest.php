<?php

namespace Tawk\Tests\UITest\Firefox;

use Tawk\Tests\TestFiles\Coverages\PluginSetup;
use Tawk\Tests\TestFiles\Helpers\Webdriver;

class FirefoxPluginSetupTest extends PluginSetup {
	public function create_driver() {
		return Webdriver::createFirefox();
	}
}
