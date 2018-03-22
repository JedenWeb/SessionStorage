<?php

namespace JedenWeb\SessionStorage\DI;

use JedenWeb\SessionStorage\DatabaseStorage;

use Nette\DI\CompilerExtension;

/**
 * @author Pavel Jurásek <jurasekpavel@ctyrimedia.cz>
 */
class DatabaseStorageExtension extends CompilerExtension
{

	public function loadConfiguration()
	{
		$container = $this->getContainerBuilder();

		$definition = $container->addDefinition($this->prefix('databaseStorage'))
			->setClass(DatabaseStorage::class);

		$container->getDefinition('session')
			->addSetup('setStorage', [$definition]);
	}

}
