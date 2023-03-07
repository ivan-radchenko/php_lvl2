<?php

use Ivan\Php\Blog\Command\Arguments;
use Ivan\Php\Blog\Command\CreateUserCommand;
use Ivan\Php\Blog\Command\CreatePostCommand;
use Ivan\Php\Blog\Command\CreateCommentCommand;
use Ivan\Php\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use Ivan\Php\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use Ivan\Php\Blog\Repositories\CommentsRepository\SqliteCommentsRepository;
use Ivan\Php\Blog\UUID;

include __DIR__ . "/vendor/autoload.php";

//Создаём объект подключения к SQLite
$connection = new PDO('sqlite:' . __DIR__ . '/blog.sqlite');

$postsRepository = new SqlitePostsRepository($connection);
var_dump($postsRepository->get(new UUID('cd3e7bf6-4cf8-4460-a7c3-0ba1836ceabd')));

die();

$route = $argv[1];

switch ($route) {
    case "user":
        $usersRepository = new SqliteUsersRepository($connection);
        $command = new CreateUserCommand($usersRepository);
        
        try {
            $command->handle(Arguments::fromArgv($argv));
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        break;

    case "post":
        $postsRepository = new SqlitePostsRepository($connection);
        $usersRepository = new SqliteUsersRepository($connection);
        $command = new CreatePostCommand($postsRepository, $usersRepository);

        try {
            $command->handle(Arguments::fromArgv($argv));
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        break;

    case "comment":
        $commentsRepository = new SqliteCommentsRepository($connection);
        $command = new CreateCommentCommand($commentsRepository);
        try {
            $command->handle(Arguments::fromArgv($argv));
        } catch (Exception $e) {
            echo $e->getMessage();
        } 
        break;

    default:
        echo "error try user post comment parameter";
}



