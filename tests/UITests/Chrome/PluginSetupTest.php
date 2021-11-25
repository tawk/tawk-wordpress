<?php

namespace Tawk\Tests\UITest\Chrome;

use Tawk\Tests\TestFiles\Coverages\PluginSetup;
use Tawk\Tests\TestFiles\Helpers\Webdriver;

class ChromePluginSetupTest extends PluginSetup {
	public function create_driver() {
		return Webdriver::createChrome();
	}
}
