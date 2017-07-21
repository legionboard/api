<?php
use \mageekguy\atoum;

$report = $script->addDefaultReport();
$runner->addTestsFromDirectory('tests/units');

$testGenerator = new atoum\test\generator();
$testGenerator->setTestClassesDirectory('tests/units');
$testGenerator->setTestClassNamespace('tests\units');
$testGenerator->setTestedClassesDirectory('src');
$testGenerator->setTestedClassNamespace('LegionBoard');
$script->getRunner()->setTestGenerator($testGenerator);
?>
