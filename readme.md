# Database SessionStorage

Database SessionStorage for [Nette Framework](http://nette.org/)

## Instalation

The best way to install jedenweb/session-storage is using  [Composer](http://getcomposer.org/):


```json
{
	"require": {
		"jedenweb/session-storage": "dev-master"
	}
}
```

After that you have to register extension in config.neon.

```neon
extensions:
	databaseSessionStorage: JedenWeb\SessionStorage\DI\DatabaseSessionStorageExtension
```

## Usage

1. Profit

## Issues

I don't know how to [prevent Nette\Http\SessionSection from triggering error]((http://api.nette.org/2.1/source-Http.SessionSection.php.html#201)).
