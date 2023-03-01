<?php

namespace Ivan\Php\Blog\Repositories\PostsRepository;

use Ivan\Php\Blog\Exceptions\InvalidArgumentException;
use Ivan\Php\Blog\Exceptions\PostNotFoundExeption;
use Ivan\Php\Blog\Exceptions\UserNotFoundException;
use Ivan\Php\Blog\Post;
use Ivan\Php\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use Ivan\Php\Blog\UUID;

class SqlitePostsRepository implements PostsRepositoryInterface
{
    private \PDO $connection;
    public function __construct(\PDO $connection) {
        $this->connection = $connection;
    }

    public function save(Post $post): void
    {
        $statement = $this->connection->prepare(
            'INSERT INTO posts (uuid,author_uuid,title,text) 
                VALUES (:uuid,:author_uuid,:title,:text)'
        );

        $statement->execute([
            ':uuid' => $post->getUuid(),
            ':author_uuid' => $post->getUser()->uuid(),
            ':title' => $post->getTitle(),
            ':text' => $post->getText(),
        ]);

    }

    /**
     * @throws InvalidArgumentException
     * @throws PostNotFoundExeption
     * @throws UserNotFoundException
     */
    public function get(UUID $uuid): Post
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM posts WHERE uuid = :uuid'
        );
        $statement->execute([
           ':uuid' => (string)$uuid,
        ]);
        return $this->getPost($statement,$uuid);
    }

    /**
     * @throws PostNotFoundExeption
     * @throws InvalidArgumentException|UserNotFoundException
     */
    private function getPost(\PDOStatement $statement, string $postUuid):Post{
          $result = $statement->fetch(\PDO::FETCH_ASSOC);

          if ($result === false) {
              throw new PostNotFoundExeption(
                  "cannot find post: $postUuid"
              );
          }
        //test
        /*print_r($result);
        die();*/
          $userRepository = new SqliteUsersRepository($this->connection);
          $user = $userRepository->get(new UUID($result['author_uuid']));

        return new Post(
            new UUID($result['author_uuid']),
            $user,
            $result['title'],
            $result['text'],
        );
    }
}