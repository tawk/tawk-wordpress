<?php

namespace Tawk\Tests\UITest\Firefox;

use Tawk\Tests\TestFiles\Coverages\Visibility;
use Tawk\Tests\TestFiles\Helpers\Webdriver;

class FirefoxVisibilityTest extends Visibility {
	public function create_driver(): void {
		$this->driver = Webdriver::createFirefox();
	}
}
