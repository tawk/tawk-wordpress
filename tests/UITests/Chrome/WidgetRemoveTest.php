<?php

namespace Tawk\Tests\UITest\Chrome;

use Tawk\Tests\TestFiles\Coverages\WidgetRemove;
use Tawk\Tests\TestFiles\Helpers\Webdriver;

class ChromeWidgetRemoveTest extends WidgetRemove {
	public function create_driver(): void {
		$this->driver = Webdriver::createChrome();
	}
}
