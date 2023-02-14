<?php

use Journal\Blog\Commands\Arguments;
use Journal\Blog\Commands\CreateUserCommand;
use Journal\Blog\Exceptions\AppException;
use Psr\Log\LoggerInterface;

require_once __DIR__ . '/vendor/autoload.php';

$container = require __DIR__ . '/bootstrap.php';
// При помощи контейнера создаём команду

$logger = $container->get(LoggerInterface::class);
$command = $container->get(CreateUserCommand::class);

try {
    $command->handler(Arguments::fromArgv($argv));
} catch (Exception $e) {
    $logger->ERROR($e->getMessage(), ['exception' => $e]);
}