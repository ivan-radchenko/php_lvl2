<?php

namespace Ivan\Php\Blog\Repositories\CommentsRepository;

use Ivan\Php\Blog\Comment;
use Ivan\Php\Blog\Exceptions\CommentNotFoundExeption;
use Ivan\Php\Blog\Exceptions\InvalidArgumentException;
use Ivan\Php\Blog\Exceptions\PostNotFoundExeption;
use Ivan\Php\Blog\Exceptions\UserNotFoundException;
use Ivan\Php\Blog\Post;
use Ivan\Php\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use Ivan\Php\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use Ivan\Php\Blog\UUID;

class SqliteCommentsRepository implements CommentsRepositoryInterface
{
    private \PDO $connection;
    public function __construct(\PDO $connection) {
        $this->connection = $connection;
    }

    public function save(Comment $comment): void
    {
        $statement = $this->connection->prepare(
            'INSERT INTO comments (uuid,post_uuid,author_uuid,text) 
                VALUES (:uuid,:post_uuid,:author_uuid,:text)'
        );

        $statement->execute([
            ':uuid' => $comment->getUuid(),
            ':post_uuid' => $comment->getPost()->uuid(),
            ':author_uuid' => $comment->getUser()->uuid(),
            ':text' => $comment->getText(),
        ]);

    }

    public function get(UUID $uuid): Comment
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM comments WHERE uuid = :uuid'
        );
        $statement->execute([
           ':uuid' => (string)$uuid,
        ]);
        return $this->getComment($statement,$uuid);
    }

    /**
     * @throws InvalidArgumentException|UserNotFoundException
     * @throws CommentNotFoundExeption
     * @throws PostNotFoundExeption
     */
    private function getComment(\PDOStatement $statement, string $commentUuid):Post{
          $result = $statement->fetch(\PDO::FETCH_ASSOC);

          if ($result === false) {
              throw new CommentNotFoundExeption(
                  "cannot find comment: $commentUuid"
              );
          }
        //test
        /*print_r($result);
        die();*/
          $userRepository = new SqliteUsersRepository($this->connection);
          $user = $userRepository->get(new UUID($result['author_uuid']));
          $postRepository = new SqlitePostsRepository($this->connection);
          $post = $postRepository->get(new UUID($result['post_uuid']));

        return new Post(
            new UUID($result['post_uuid']),
            $user,
            $post,
            $result['text'],
        );
    }
}