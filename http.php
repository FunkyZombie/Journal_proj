<?php

use Journal\Blog\Exceptions\HttpException;
use Journal\Http\Actions\Comments\CreateComment;
use Journal\Http\Actions\Like\CreateLike;
use Journal\Http\Actions\Posts\CreatePost;
use Journal\Http\Actions\Posts\DeletePost;
use Journal\Http\Actions\Posts\FindByUuid;
use Journal\Http\Actions\Users\CreateUser;
use Journal\Http\Actions\Users\FindByUsername;
use Journal\Http\ErrorResponse;
use Journal\Http\Request;

require_once __DIR__ . '/vendor/autoload.php';
$container = require __DIR__ . '/bootstrap.php';

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
        '/users/show' => FindByUsername::class,
        '/posts/show' => FindByUuid::class
    ],
    'POST' => [
        '/posts/create' => CreatePost::class,
        '/posts/comment' => CreateComment::class,
        '/posts/like' => CreateLike::class,
        '/users/create' => CreateUser::class,
        
    ],
    'DELETE' => [
        '/posts' => DeletePost::class,
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

$actionClassName = $routes[$method][$path];
$action = $container->get($actionClassName);

try {
    $response = $action->handle($request);
} catch (Exception $e) {
    (new ErrorResponse($e->getMessage()))->send();
}

$response->send();