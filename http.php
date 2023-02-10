<?php

use Journal\Blog\Exceptions\HttpException;
use Journal\Blog\Repositories\PostRepository\SqliteCommentsRepository;
use Journal\Blog\Repositories\PostRepository\SqlitePostsRepository;
use Journal\Blog\Repositories\UserRepository\SqliteUsersRepository;
use Journal\Http\Actions\Comments\CreateComment;
use Journal\Http\Actions\Posts\CreatePost;
use Journal\Http\Actions\Posts\DeletePost;
use Journal\Http\Actions\Posts\FindByUuid;
use Journal\Http\Actions\Users\CreateUser;
use Journal\Http\Actions\Users\FindByUsername;
use Journal\Http\ErrorResponse;
use Journal\Http\Request;

require_once __DIR__ . '/vendor/autoload.php';

$request = new Request(
    $_GET,
    $_SERVER,
    file_get_contents('php://input'),
);
try {
    $path = $request->path();
} catch (HttpException) {
    (new ErrorResponse)->send();
    return;
}

try {
    $method = $request->method();
} catch (HttpException) {
    (new ErrorResponse)->send();
    return;
}

$routes = [
    'GET' => [
        '/users/show' => new FindByUsername(
            new SqliteUsersRepository(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            )
        ),
        '/posts/show' => new FindByUuid(
            new SqlitePostsRepository(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite'),
                new SqliteUsersRepository(
                    new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
                )
            )
        ),
    ],
    'POST' => [
        '/posts/create' => new CreatePost(
            new SqlitePostsRepository(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite'),
                new SqliteUsersRepository(
                    new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
                )
            ),
            new SqliteUsersRepository(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            )
        ),
        '/posts/comment' => new CreateComment(
            new SqlitePostsRepository(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite'),
                new SqliteUsersRepository(
                    new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
                )
            ),
            new SqliteUsersRepository(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            ),
            new SqliteCommentsRepository(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            ),
        ),
        '/users/create' => new CreateUser(
            new SqliteUsersRepository(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            )
        ),
    ],
    'DELETE' => [
        '/posts' => new DeletePost(
            new SqlitePostsRepository(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite'),
                new SqliteUsersRepository(
                    new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
                )
            ),
        ),
    ],
];

if (!array_key_exists($method, $routes)) {
    (new ErrorResponse('Not found'))->send();
    return;
}

if (!array_key_exists($path, $routes[$method])) {
    (new ErrorResponse('Not found'))->send();
    return;
}

$action = $routes[$method][$path];

try {
    $response = $action->handle($request);
    $response->send();
} catch (Exception $e) {
    (new ErrorResponse($e->getMessage()))->send();
}
