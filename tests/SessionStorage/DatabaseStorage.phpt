<?php

/**
 * @dataProvider? databases.ini
 */

use Nette\Http\Session;
use Tester\Assert;


//$container = require __DIR__ . '/../../bootstrap.php';
require __DIR__ . '/connect.inc.php';


Nette\Database\Helpers::loadFromFile($connection, __DIR__ . "/files/{$driverName}-init.sql");


$storage = new JedenWeb\SessionStorage\DatabaseStorage($context);
$storage->install();

$factory = new Nette\Http\RequestFactory;
$session = new Nette\Http\Session($factory->createHttpRequest(), new Nette\Http\Response);

$session->setStorage($storage);


$session->start();
$_COOKIE['PHPSESSID'] = $session->getId();

$namespace = $session->getSection('one');
$namespace->a = 'apple';
$session->close();
unset($_SESSION);

$session->start();
$namespace = $session->getSection('one');
Assert::same('apple', $namespace->a);

Assert::true((bool) $context->table('session')->get($session->getId()));
Assert::equal(1, $context->table('session')->count());
