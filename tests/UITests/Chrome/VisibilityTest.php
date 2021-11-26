<?php

namespace Tawk\Tests\UITest\Chrome;

use Tawk\Tests\TestFiles\Coverages\Visibility;
use Tawk\Tests\TestFiles\Helpers\Webdriver;

class ChromeVisibilityTest extends Visibility {
	public function create_driver(): void {
		$this->driver = Webdriver::createChrome();
	}
}
