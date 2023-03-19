<?php

use Dotenv\Dotenv;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Monolog\Handler\StreamHandler;
use Ivan\Php\Blog\Container\DIContainer;
use Ivan\Php\Http\Auth\PasswordAuthentication;
use Ivan\Php\Http\Auth\AuthenticationInterface;
use Ivan\Php\Http\Auth\IdentificationInterface;
use Ivan\Php\Http\Auth\BearerTokenAuthentication;
use Ivan\Php\Http\Auth\TokenAuthenticationInterface;
use Ivan\Php\Http\Auth\JsonBodyUsernameIdentification;
use Ivan\Php\Http\Auth\PasswordAuthenticationInterface;
use Ivan\Php\Blog\Repositories\LikesRepository\SqliteLikesRepository;
use Ivan\Php\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use Ivan\Php\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use Ivan\Php\Blog\Repositories\LikesRepository\LikesRepositoryInterface;
use Ivan\Php\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use Ivan\Php\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use Ivan\Php\Blog\Repositories\CommentsRepository\SqliteCommentsRepository;
use Ivan\Php\Blog\Repositories\CommentsRepository\CommentsRepositoryInterface;
use Ivan\Php\Blog\Repositories\AuthTokensRepository\SqliteAuthTokensRepository;
use Ivan\Php\Blog\Repositories\AuthTokensRepository\AuthTokensRepositoryInterface;

require_once __DIR__ . '/vendor/autoload.php';

// Загружаем переменные окружения из файла .env
Dotenv::createImmutable(__DIR__)->safeLoad();

$container = new DIContainer();

$container->bind(
    PDO::class,
    // Берём путь до файла базы данных SQLite
    // из переменной окружения SQLITE_DB_PATH
    new PDO('sqlite:' . __DIR__ . '/' . $_SERVER['SQLITE_DB_PATH'])
);


$logger = new Logger('blog');


if ('yes' === $_ENV['LOG_TO_FILES']) {
    $logger->pushHandler(new StreamHandler(
        __DIR__ . '/logs/blog.log'
    ))
        ->pushHandler(new StreamHandler(
            __DIR__ . '/logs/blog.error.log',
            level: Logger::ERROR,
            bubble: false,
        ));
}

if ('yes' === $_ENV['LOG_TO_CONSOLE']) {
    $logger->pushHandler(
        new StreamHandler("php://stdout")
    );
}

$container->bind(
    CommentsRepositoryInterface::class,
    SqliteCommentsRepository::class
);

$container->bind(
    PostsRepositoryInterface::class,
    SqlitePostsRepository::class
);

$container->bind(
    UsersRepositoryInterface::class,
    SqliteUsersRepository::class
);

$container->bind(
    LikesRepositoryInterface::class,
    SqliteLikesRepository::class
);

$container->bind(
    LoggerInterface::class,
    $logger

);

$container->bind(
    TokenAuthenticationInterface::class,
    BearerTokenAuthentication::class
);


$container->bind(
    PasswordAuthenticationInterface::class,
    PasswordAuthentication::class
);
$container->bind(
    AuthTokensRepositoryInterface::class,
    SqliteAuthTokensRepository::class
);

$container->bind(
    AuthenticationInterface::class,
    PasswordAuthentication::class
);

$container->bind(
    IdentificationInterface::class,
    JsonBodyUsernameIdentification::class
);


return $container;
