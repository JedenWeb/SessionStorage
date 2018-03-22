# Database SessionStorage

Database SessionStorage for [Nette Framework](http://nette.org/)

## Instalation

The best way to install jedenweb/session-storage is using  [Composer](http://getcomposer.org/):

```sh
$ composer require jedenweb/session-storage:~2.1.0
```

After that you have to register extension in config.neon.

```neon
extensions:
	sessionStorage: JedenWeb\SessionStorage\DI\DatabaseStorageExtension
```

Add table to your database

```
CREATE TABLE IF NOT EXISTS `session` (
	`id` varchar(64) NOT NULL,
	`timestamp` int(11) NOT NULL,
	`data` longtext NOT NULL,
	PRIMARY KEY (`id`),
	KEY `timestamp` (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
```
