<?php

namespace Ivan\Php\Blog\Repositories\UsersRepository;

use \PDO;
use \PDOStatement;
use Psr\Log\LoggerInterface;
use Ivan\Php\Blog\User;
use Ivan\Php\Blog\UUID;
use Ivan\Php\Person\Name;
use Ivan\Php\Blog\Exceptions\UserNotFoundException;
use Ivan\Php\Blog\Exceptions\InvalidArgumentException;

class SqliteUsersRepository implements UsersRepositoryInterface
{
    private PDO $connection;
    private LoggerInterface $logger;

    public function __construct(PDO $connection, LoggerInterface $logger)
    {
        $this->connection = $connection;
        $this->logger = $logger;
    }


    public function save(User $user): void
    {

        // Подготавливаем запрос
        $statement = $this->connection->prepare(
            'INSERT INTO users (first_name, last_name, uuid, username) 
            VALUES (:first_name, :last_name, :uuid, :username)'

        );
        // Выполняем запрос с конкретными значениями
        $statement->execute([
            ':first_name' => $user->name()->first(),
            ':last_name' => $user->name()->last(),
            ':uuid' => (string)$user->uuid(),
            ':username' => $user->username(),
        ]);
        $this->logger->info("User created successfully: {$user->uuid()}");
    }

    // Также добавим метод для получения
    // пользователя по его UUID
    /**
     * @throws UserNotFoundException
     * @throws InvalidArgumentException
     */
    public function get(UUID $uuid): User
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM users WHERE uuid = ?'
        );

        $statement->execute([(string)$uuid]);

        return $this->getUser($statement, $uuid);
    }

    /**
     * @throws UserNotFoundException
     * @throws InvalidArgumentException
     */
    public function getByUsername(string $username): User
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM users WHERE username = :username'
        );
        $statement->execute([
            ':username' => $username,
        ]);

        return $this->getUser($statement, $username);
    }

    /**
     * @throws UserNotFoundException
     * @throws InvalidArgumentException
     */
    private function getUser(PDOStatement $statement, string $errorString): User
    {
        $result = $statement->fetch(\PDO::FETCH_ASSOC);
        if ($result === false) {
            $message = "Cannot find user: $errorString";
            $this->logger->warning($message);
            throw new UserNotFoundException($message);
        }
        // Создаём объект пользователя с полем username
        return new User(
            new UUID($result['uuid']),
            new Name($result['first_name'], $result['last_name']),
            $result['username'],
        );
    }
}
