<?php

namespace Tawk\Tests\UITest\Firefox;

use Tawk\Tests\TestFiles\Coverages\PluginUninstall;
use Tawk\Tests\TestFiles\Helpers\Webdriver;

class FirefoxPluginUninstallTest extends PluginUninstall {
	public function create_driver(): void {
		$this->driver = Webdriver::createFirefox();
	}
}
