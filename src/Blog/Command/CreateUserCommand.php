<?php

namespace Ivan\Php\Blog\Command;

use Ivan\Php\Person\Name;
use Ivan\Php\Blog\Exceptions\ArgumentsException;
use Ivan\Php\Blog\Exceptions\CommandException;
use Ivan\Php\Blog\Exceptions\InvalidArgumentException;
use Ivan\Php\Blog\Exceptions\UserNotFoundException;
use Ivan\Php\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use Ivan\Php\Blog\User;
use Ivan\Php\Blog\UUID;
use Psr\Log\LoggerInterface;

//php cli.php user username=ivan first_name=Ivan last_name=Nikitin

class CreateUserCommand
{

    // Команда зависит от контракта репозитория пользователей,
    // а не от конкретной реализации
    public function __construct(
        private UsersRepositoryInterface $usersRepository,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @throws CommandException
     * @throws InvalidArgumentException|ArgumentsException
     */
    public function handle(Arguments $arguments): void
    {
        $this->logger->info("Create user command started");

        $username = $arguments->get('username');
        // Проверяем, существует ли пользователь в репозитории
        if ($this->userExists($username)) {

            $this->logger->warning("User already exists: $username");
            throw new CommandException("User already exists: $username");
        }

        $uuid = UUID::random();

        // Сохраняем пользователя в репозиторий
        $this->usersRepository->save(new User(
            $uuid,
            new Name(
                $arguments->get('first_name'),
                $arguments->get('last_name')
            ),
            $username,
            $arguments->get('password'),
        ));

        // Логируем информацию о новом пользователе
        $this->logger->info("User created: $uuid");
    }

    private function userExists(string $username): bool
    {
        try {
            // Пытаемся получить пользователя из репозитория
            $this->usersRepository->getByUsername($username);
        } catch (UserNotFoundException) {
            return false;
        }
        return true;
    }
}
