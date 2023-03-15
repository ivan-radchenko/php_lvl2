<?php


use Psr\Log\LoggerInterface;
use Ivan\Php\Http\Request;
use Ivan\Php\Http\ErrorResponse;
use Ivan\Php\Blog\Exceptions\AppException;
use Ivan\Php\Blog\Exceptions\HttpException;
use Ivan\Php\Http\Actions\Posts\CreatePost;
use Ivan\Php\Http\Actions\Posts\DeletePost;
use Ivan\Php\Http\Actions\Posts\FindByUuid;
use Ivan\Php\Http\Actions\Users\CreateUser;
use Ivan\Php\Http\Actions\Likes\CreatePostLike;
use Ivan\Php\Http\Actions\Users\FindByUsername;
use Ivan\Php\Http\Actions\Comments\CreateComment;

$container = require __DIR__ . '/bootstrap.php';

$logger = $container->get(LoggerInterface::class);

$request = new Request(
    $_GET,
    $_SERVER,
    file_get_contents('php://input'),
);

try {
    $path = $request->path();
} catch (HttpException $e) {
    $logger->warning($e->getMessage());
    (new ErrorResponse)->send();
    return;
}

try {
    $method = $request->method();
} catch (HttpException $e) {
    $logger->warning($e->getMessage());
    (new ErrorResponse)->send();
    return;
}


$routes = [
    'GET' => [
        '/users/show' => FindByUsername::class,
    ],
    'POST' => [
        '/login' => LogIn::class,
        '/users/create' => CreateUser::class,
        '/posts/create' => CreatePost::class,
        '/post-likes/create' => CreatePostLike::class,
    ],
    'DELETE' => [
        '/posts' => DeletePost::class,
    ],

];

if (!array_key_exists($method, $routes) || !array_key_exists($path, $routes[$method])) {
    // Логируем сообщение с уровнем NOTICE
    $message = "Route not found: $method $path";
    $logger->notice($message);
    (new ErrorResponse($message))->send();
    return;
}

$actionClassName = $routes[$method][$path];

$action = $container->get($actionClassName);

try {
    $response = $action->handle($request);
} catch (AppException $e) {
    $logger->error($e->getMessage(), ['exception' => $e]);
    (new ErrorResponse($e->getMessage()))->send();
    return;
}

$response->send();
