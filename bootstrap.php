<?php

use Dotenv\Dotenv;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Monolog\Handler\StreamHandler;
use Ivan\Php\Blog\Container\DIContainer;
use Ivan\Php\Blog\Command\CreateUserCommand;
use Ivan\Php\Blog\Repositories\LikesRepository\SqliteLikesRepository;
use Ivan\Php\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use Ivan\Php\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use Ivan\Php\Blog\Repositories\LikesRepository\LikesRepositoryInterface;
use Ivan\Php\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use Ivan\Php\Blog\Repositories\UsersRepository\UsersRepositoryInterface;

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


return $container;
