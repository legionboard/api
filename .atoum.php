<?php
use \mageekguy\atoum;

$report = $script->addDefaultReport();
$runner->addTestsFromDirectory('src/tests/units');

$testGenerator = new atoum\test\generator();
$testGenerator->setTestClassesDirectory('src/tests/units');
$testGenerator->setTestClassNamespace('LegionBoard\tests\units');
$testGenerator->setTestedClassesDirectory('src');
$testGenerator->setTestedClassNamespace('LegionBoard');
$script->getRunner()->setTestGenerator($testGenerator);
?>
