<?php

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Application;
use Ivan\Php\Blog\Command\Posts\DeletePost;
use Ivan\Php\Blog\Command\Users\CreateUser;
use Ivan\Php\Blog\Command\Users\UpdateUser;
use Ivan\Php\Blog\Command\FakeData\PopulateDB;

$container = require __DIR__ . '/bootstrap.php';

$logger = $container->get(LoggerInterface::class);


// Создаём объект приложения
$application = new Application();

// Перечисляем классы команд
$commandsClasses = [
    CreateUser::class,
    DeletePost::class,
    UpdateUser::class,
    PopulateDB::class,
];

foreach ($commandsClasses as $commandClass) {
    // Посредством контейнера
    // создаём объект команды
    $command = $container->get($commandClass);
    // Добавляем команду к приложению
    $application->add($command);
}

try {

    $application->run();
} catch (Exception $e) {
    $logger->error($e->getMessage(), ['exception' => $e]);
    echo $e->getMessage();
}
