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

## Issues

 - [ ] I don't know how to [prevent Nette\Http\SessionSection from triggering error](http://api.nette.org/2.1.4/source-Http.SessionSection.php.html#197) (should be solved by https://github.com/nette/nette/pull/1395 in Nette ~2.2).
 - [ ] TODO: Don't call ```DatabaseStorage::install()``` on every request but only once on compile time.
