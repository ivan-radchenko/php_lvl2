<?php

use Ivan\Php\Blog\Command\Arguments;
use Ivan\Php\Blog\Command\CreateUserCommand;
use Ivan\Php\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use Ivan\Php\Blog\UUID;
use Ivan\Php\Blog\Repositories\PostsRepository\SqlitePostsRepository;

include __DIR__ . "/vendor/autoload.php";

//Создаём объект подключения к SQLite
$connection = new PDO('sqlite:' . __DIR__ . '/blog.sqlite');

$usersRepository = new SqliteUsersRepository($connection);
$postsRepository = new SqlitePostsRepository($connection);

/*$command = new CreateUserCommand($usersRepository);

try {
    $command->handle(Arguments::fromArgv($argv));
} catch (Exception $e) {
    echo $e->getMessage();
}*/

//test
try {
    $user = $usersRepository->get(new UUID('21a6bc3b-a1e7-4a1e-b774-d1276e67cfef'));


    //save
    /*$post = new Post(
        UUID::random(),
        $user,
        'zagolovok',
        'text posta',
    );

    $postsRepository->save($post);*/

    //get
    $post = $postsRepository->get(new UUID('fd5d306c-ec32-46a5-98c2-a2d3935401a8'));
    print_r($post);

} catch (Exception $e) {
    echo $e->getMessage();
}

