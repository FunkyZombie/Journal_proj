<?php


use Journal\Blog\Commands\Users\CreateUser;
use Journal\Blog\Commands\Posts\DeletePost;
use Journal\Blog\Commands\Users\UpdateUser;
use Journal\Blog\Commands\FakeData\PopulateDB;
use Symfony\Component\Console\Application;


require_once __DIR__ . '/vendor/autoload.php';
$container = require __DIR__ . '/bootstrap.php';

$application = new Application();

$commandsClasses = [
    CreateUser::class,
    DeletePost::class,
    UpdateUser::class,
    PopulateDB::class,
];

foreach ($commandsClasses as $commandClass) {
    $command = $container->get($commandClass);
    $application->add($command);
}

$application->run();
