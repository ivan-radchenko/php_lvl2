<?php

use Ivan\Php\Blog\Command\Arguments;
use Ivan\Php\Blog\Command\CreateUserCommand;
use Ivan\Php\Blog\Repositories\UsersRepository\SqliteUsersRepository;


include __DIR__ . "/vendor/autoload.php";

//Создаём объект подключения к SQLite
$connection = new PDO('sqlite:' . __DIR__ . '/blog.sqlite');

$usersRepository = new SqliteUsersRepository($connection);

$command = new CreateUserCommand($usersRepository);

try {
    $command->handle(Arguments::fromArgv($argv));
} catch (Exception $e) {
    echo $e->getMessage();
}