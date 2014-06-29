<?php

namespace JedenWeb\SessionStorage\DI;

use Nette;

/**
 * @author Pavel Jurásek <jurasekpavel@ctyrimedia.cz>
 */
class DatabaseStorageExtension extends Nette\DI\CompilerExtension
{

	public function loadConfiguration()
	{
		$container = $this->getContainerBuilder();

		$definition = $container->addDefinition($this->prefix('databaseStorage'))
				->setClass('JedenWeb\SessionStorage\DatabaseStorage')
				->addSetup('install');

		$container->getDefinition('session')
				->addSetup('setStorage', array($definition));
	}

}
