<?php

namespace Tawk\Tests\UITest\Chrome;

use Tawk\Tests\TestFiles\Coverages\PluginUninstall;
use Tawk\Tests\TestFiles\Helpers\Webdriver;

class ChromePluginUninstallTest extends PluginUninstall {
	public function create_driver(): void {
		$this->driver = Webdriver::createChrome();
	}
}
