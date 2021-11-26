<?php

namespace Tawk\Tests\UITest\Firefox;

use Tawk\Tests\TestFiles\Coverages\WidgetSet;
use Tawk\Tests\TestFiles\Helpers\Webdriver;

class FirefoxWidgetSetTest extends WidgetSet {
	public function create_driver(): void {
		$this->driver = Webdriver::createFirefox();
	}
}
