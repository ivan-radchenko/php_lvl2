<?php

use Ivan\Php\Blog\Command\Arguments;
use Ivan\Php\Blog\Command\CreateUserCommand;
use Ivan\Php\Blog\Comment;
use Ivan\Php\Blog\Repositories\CommentsRepository\SqliteCommentsRepository;
use Ivan\Php\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use Ivan\Php\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use Ivan\Php\Blog\UUID;


include __DIR__ . "/vendor/autoload.php";

//Создаём объект подключения к SQLite
$connection = new PDO('sqlite:' . __DIR__ . '/blog.sqlite');

$usersRepository = new SqliteUsersRepository($connection);
$postsRepository = new SqlitePostsRepository($connection);
$commentsRepository = new SqliteCommentsRepository($connection);

/*$command = new CreateUserCommand($usersRepository);

try {
    $command->handle(Arguments::fromArgv($argv));
} catch (Exception $e) {
    echo $e->getMessage();
}*/

//test
try {
    $user = $usersRepository->get(new UUID('21a6bc3b-a1e7-4a1e-b774-d1276e67cfef'));
    $post = $postsRepository->get(new UUID('fd5d306c-ec32-46a5-98c2-a2d3935401a8'));

    //save
    $comment = new Comment(
        UUID::random(),
        $post,
        $user,
        'text commenta',
    );


    $commentsRepository->save($comment);

    //get



} catch (Exception $e) {
    echo $e->getMessage();
}

