<?php

namespace Tawk\Tests\UITest\Firefox;

use Tawk\Tests\TestFiles\Coverages\WidgetRemove;
use Tawk\Tests\TestFiles\Helpers\Webdriver;

class FirefoxWidgetRemoveTest extends WidgetRemove {
	public function create_driver(): void {
		$this->driver = Webdriver::createFirefox();
	}
}
