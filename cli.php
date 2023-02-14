<?php

use Journal\Blog\Commands\Arguments;
use Journal\Blog\Commands\CreateUserCommand;
use Journal\Blog\Exceptions\AppException;

require_once __DIR__ . '/vendor/autoload.php';

$container = require __DIR__ . '/bootstrap.php';
// При помощи контейнера создаём команду
$command = $container->get(CreateUserCommand::class);
try {
    $command->handle(Arguments::fromArgv($argv));
} catch (AppException $e) {
    echo "{$e->getMessage()}\n";
}