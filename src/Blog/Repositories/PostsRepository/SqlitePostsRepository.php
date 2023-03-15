<?php

namespace Ivan\Php\Blog\Repositories\PostsRepository;

use \PDO;
use \PDOStatement;
use Psr\Log\LoggerInterface;
use Ivan\Php\Blog\Post;
use Ivan\Php\Blog\UUID;
use Ivan\Php\Blog\Exceptions\PostNotFoundException;
use Ivan\Php\Blog\Repositories\UsersRepository\SqliteUsersRepository;

class SqlitePostsRepository implements PostsRepositoryInterface
{
    private PDO $connection;
    private LoggerInterface $logger;

    public function __construct(PDO $connection, LoggerInterface $logger)
    {
        $this->connection = $connection;
        $this->logger = $logger;
    }

    public function save(Post $post): void
    {

        $statement = $this->connection->prepare(
            'INSERT INTO posts (uuid, author_uuid, title, text) 
            VALUES (:uuid, :author_uuid, :title, :text)'

        );
        $statement->execute([
            ':uuid' => (string)$post->uuid(),
            ':author_uuid' => (string)$post->user()->uuid(),
            ':title' => $post->title(),
            ':text' => $post->text(),
        ]);

        $this->logger->info("Post created successfully: {$post->uuid()}");
    }

    public function get(UUID $uuid): Post
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM posts WHERE uuid = :uuid'
        );
        $statement->execute([
            ':uuid' => (string)$uuid,
        ]);

        return $this->getPost($statement, $uuid);
    }

    private function getPost(PDOStatement $statement, string $errorString): Post
    {
        $result = $statement->fetch(\PDO::FETCH_ASSOC);
        if ($result === false) {
            $message = "Cannot find post: $errorString";
            $this->logger->warning($message);
            throw new PostNotFoundException($message);
        }

        $userRepository = new SqliteUsersRepository($this->connection, $this->logger);
        $user = $userRepository->get(new UUID($result['author_uuid']));

        return new Post(
            new UUID($result['uuid']),
            $user,
            $result['title'],
            $result['text']
        );
    }

    public function delete(UUID $uuid): void
    {
        $statement = $this->connection->prepare(
            'DELETE FROM posts WHERE posts.uuid=:uuid;'
        );

        $statement->execute([
            ':uuid' => $uuid,
        ]);
    }
}
