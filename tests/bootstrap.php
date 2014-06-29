<?php

require __DIR__ . '/../vendor/autoload.php';

if (!class_exists('Tester\Assert')) {
	echo "Install Nette Tester using `composer update --dev`\n";
	exit(1);
}

Tester\Environment::setup();

define('TEMP_DIR', __DIR__ . '/temp');

//\Tester\Helpers::purge(__DIR__ . '/temp');

$configurator = new Nette\Configurator;
$configurator->setDebugMode(FALSE);
$configurator->setTempDirectory(__DIR__ . '/temp');
$configurator->createRobotLoader()
	->addDirectory(__DIR__ . '/../src')
	->register();

return $configurator->createContainer();
