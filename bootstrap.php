<?php
use Journal\Blog\Container\DIContainer;
use Journal\Blog\Repositories\LikeRepository\LikeRepositoryInterface;
use Journal\Blog\Repositories\LikeRepository\SqliteLikeRepository;
use Journal\Blog\Repositories\PostRepository\CommentRepositoryInterface;
use Journal\Blog\Repositories\PostRepository\PostRepositoryInterface;
use Journal\Blog\Repositories\PostRepository\SqliteCommentsRepository;
use Journal\Blog\Repositories\PostRepository\SqlitePostsRepository;
use Journal\Blog\Repositories\UserRepository\SqliteUsersRepository;
use Journal\Blog\Repositories\UserRepository\UserRepositoryInterface;

require_once __DIR__ . '/vendor/autoload.php';

$container = new DIContainer();
// 1. подключение к БД
$container->bind(
    PDO::class,
    new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
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

$container->bind(
    LikeRepositoryInterface::class,
    SqliteLikeRepository::class
);

return $container;