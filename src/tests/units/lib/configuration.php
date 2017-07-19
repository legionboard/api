<?php

namespace LegionBoard\tests\units\Lib;

require_once __DIR__ . '/../../../lib/configuration.php';

use atoum;

class Configuration extends atoum {

	public function testGetEmpty() {
		$this
			->assert('empty configuration')
			->if($this->newTestedInstance)
			->then
				->string($this->testedInstance->get(null, null))
					->isEqualTo('');
	}
}
?>
