<?php

namespace Tawk\Tests\UITest\Chrome;

use Tawk\Tests\TestFiles\Coverages\WidgetSet;
use Tawk\Tests\TestFiles\Helpers\Webdriver;

class ChromeWidgetSetTest extends WidgetSet {
	public function create_driver(): void {
		$this->driver = Webdriver::createChrome();
	}
}
