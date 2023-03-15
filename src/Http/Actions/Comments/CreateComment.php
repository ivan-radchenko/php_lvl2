<?php

namespace Ivan\Php\Http\Actions\Comments;

use Ivan\Php\Blog\Exceptions\InvalidArgumentException;
use Ivan\Php\Http\Actions\ActionInterface;
use Ivan\Php\Http\ErrorResponse;
use Ivan\Php\Blog\Exceptions\HttpException;
use Ivan\Php\Http\Request;
use Ivan\Php\Http\Response;
use Ivan\Php\http\SuccessfulResponse;
use Ivan\Php\Blog\Comment;
use Ivan\Php\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use Ivan\Php\Blog\Exceptions\UserNotFoundException;
use Ivan\Php\Blog\Exceptions\PostNotFoundException;
use Ivan\Php\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use Ivan\Php\Blog\UUID;
use Ivan\Php\Blog\Repositories\CommentsRepository\CommentsRepositoryInterface;

class CreateComment implements ActionInterface
{
    public function __construct(
        private CommentsRepositoryInterface $commentsRepository,
        private PostsRepositoryInterface $postsRepository,
        private UsersRepositoryInterface $usersRepository,
    ) {
    }
    public function handle(Request $request): Response
    {
        try {
            $authorUuid = new UUID($request->jsonBodyField('author_uuid'));
        } catch (HttpException | InvalidArgumentException $e) {
            return new ErrorResponse($e->getMessage());
        }
        try {
            $user = $this->usersRepository->get($authorUuid);
        } catch (UserNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $postUuid = new UUID($request->jsonBodyField('post_uuid'));
        } catch (HttpException | InvalidArgumentException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $post = $this->postsRepository->get($postUuid);
        } catch (PostNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $newCommentUuid = UUID::random();
        try {
            $comment = new Comment(
                $newCommentUuid,
                $user,
                $post,
                $request->jsonBodyField('text'),
            );
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }
        $this->commentsRepository->save($comment);
        return new SuccessfulResponse([
            'uuid' => (string)$newCommentUuid,
        ]);
    }
}
