<?php

namespace Tawk\Tests\UITest\Chrome;

use Tawk\Tests\TestFiles\Coverages\PluginInstall;
use Tawk\Tests\TestFiles\Helpers\Webdriver;

class ChromePluginInstallTest extends PluginInstall {
	public function create_driver(): void {
		$this->driver = Webdriver::createChrome();
	}
}
