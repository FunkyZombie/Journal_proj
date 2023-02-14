<?php

use Journal\Blog\Commands\CreateUserCommand;
use Journal\Blog\Container\DIContainer;
use Journal\Blog\Repositories\LikeRepository\LikeRepositoryInterface;
use Journal\Blog\Repositories\LikeRepository\SqliteLikeRepository;
use Journal\Blog\Repositories\PostRepository\CommentRepositoryInterface;
use Journal\Blog\Repositories\PostRepository\PostRepositoryInterface;
use Journal\Blog\Repositories\PostRepository\SqliteCommentsRepository;
use Journal\Blog\Repositories\PostRepository\SqlitePostsRepository;
use Journal\Blog\Repositories\UserRepository\SqliteUsersRepository;
use Journal\Blog\Repositories\UserRepository\UserRepositoryInterface;
use Journal\Http\Auth\IdentificationInterface;
use Journal\Http\Auth\JsonBodyUuidIdentification;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

require_once __DIR__ . '/vendor/autoload.php';

\Dotenv\Dotenv::createImmutable(__DIR__)->safeLoad();

$container = new DIContainer();

$logger = (new Logger('blog'));

if ('yes' === $_SERVER['LOG_TO_FILES']) {
    $logger->pushHandler(
        new StreamHandler(
            __DIR__ . '/logs/blog.log'
        )
    )
        ->pushHandler(
            new StreamHandler(
                __DIR__ . '/logs/blog.error.log',
                level: Logger::ERROR,
                bubble: false,
            )
        );
}

if ('yes' === $_SERVER['LOG_TO_CONSOLE']) {
    $logger
        ->pushHandler(
            new StreamHandler("php://stdout")
        );
}
//LOGGER
$container->bind(
    LoggerInterface::class,
    $logger
);
// 1. подключение к БД
$container->bind(
    PDO::class,
    new PDO('sqlite:' . __DIR__ . '/' . $_SERVER['SQLITE_DB_PATH'])
);
// 2. репозиторий статей
$container->bind(
    PostRepositoryInterface::class,
    SqlitePostsRepository::class
);
// 3. репозиторий пользователей
$container->bind(
    UserRepositoryInterface::class,
    SqliteUsersRepository::class
);
// 4. репозиторий комментариев
$container->bind(
    CommentRepositoryInterface::class,
    SqliteCommentsRepository::class
);
// 5. Репозиторий лайк
$container->bind(
    LikeRepositoryInterface::class,
    SqliteLikeRepository::class
);
$container->bind(
    'CreateUserCommand',
    CreateUserCommand::class
);
$container->bind(
    IdentificationInterface::class,
    JsonBodyUuidIdentification::class
);

return $container;