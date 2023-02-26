<?php

use Ivan\Php\Blog\User;
use Ivan\Php\Person\{Name, Person};
use Ivan\Php\Blog\Post;
use Ivan\Php\Blog\Repositories\InMemoryUsersRepository;
use Ivan\Php\Blog\Exceptions\UserNotFoundException;



include __DIR__ . "/vendor/autoload.php";


$name = new Name('Иван', 'Иванов');

$user = new User(1, $name, "Admin");
echo $user;


$name = new Name('Сергей', 'Петров');
$person = new Person($name, new DateTimeImmutable());


$post = new Post(
    1,
    $person,
    'Всем привет!'
);

echo $post;

$name2 = new Name('Леша', 'Таранов');
$user2 = new User(2, $name2, "User");

$userRepository = new InMemoryUsersRepository();
try {
    $userRepository->save($user);
    $userRepository->save($user2);


    echo $userRepository->get(1);
    echo $userRepository->get(2);
    echo $userRepository->get(3);
} catch (UserNotFoundException | Exception $e) {
    echo $e->getMessage();
}