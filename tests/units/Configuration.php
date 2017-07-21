<?php

namespace tests\units\LegionBoard;

require_once __DIR__ . '/../../src/LegionBoard/Configuration.php';

use atoum;

class Configuration extends atoum
{

    public function testGetEmpty()
    {
        $this
            ->assert('empty configuration')
            ->if($this->newTestedInstance)
            ->then
                ->string($this->testedInstance->get(null, null))
                    ->isEqualTo('');
    }
}
