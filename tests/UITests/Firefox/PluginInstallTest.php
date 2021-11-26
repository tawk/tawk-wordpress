<?php

namespace Tawk\Tests\UITest\Firefox;

use Tawk\Tests\TestFiles\Coverages\PluginInstall;
use Tawk\Tests\TestFiles\Helpers\Webdriver;

class FirefoxPluginInstallTest extends PluginInstall {
	public function create_driver(): void {
		$this->driver = Webdriver::createFirefox();
	}
}
