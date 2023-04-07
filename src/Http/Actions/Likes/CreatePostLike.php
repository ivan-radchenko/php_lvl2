<?php

namespace Ivan\Php\Http\Actions\Likes;

use Ivan\Php\Blog\Like;
use Ivan\Php\Blog\UUID;
use Ivan\Php\http\Request;
use Ivan\Php\http\Response;
use Ivan\Php\Http\ErrorResponse;
use Ivan\Php\Http\SuccessfulResponse;
use Ivan\Php\Http\Actions\ActionInterface;
use Ivan\Php\Blog\Exceptions\AuthException;
use Ivan\Php\Blog\Exceptions\HttpException;
use Ivan\Php\Blog\Exceptions\LikeAlreadyExists;
use Ivan\Php\Blog\Exceptions\PostNotFoundException;
use Ivan\Php\Http\Auth\TokenAuthenticationInterface;
use Ivan\Php\Blog\Exceptions\InvalidArgumentException;
use Ivan\Php\Blog\Repositories\LikesRepository\LikesRepositoryInterface;
use Ivan\Php\Blog\Repositories\PostsRepository\PostsRepositoryInterface;

class CreatePostLike implements ActionInterface
{
    public   function __construct(
        private LikesRepositoryInterface $likesRepository,
        private PostsRepositoryInterface $postRepository,
        private TokenAuthenticationInterface $authentication,
    ) {
    }


    /**
     * @throws InvalidArgumentException
     */
    public function handle(Request $request): Response
    {
        try {
            $author = $this->authentication->user($request);
        } catch (AuthException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        try {
            $postUuid = $request->JsonBodyField('post_uuid');
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $this->postRepository->get(new UUID($postUuid));
        } catch (PostNotFoundException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        try {
            $this->likesRepository->checkUserLikeForPostExists($postUuid, $author->uuid());
        } catch (LikeAlreadyExists $e) {
            return new ErrorResponse($e->getMessage());
        }

        $newLikeUuid = UUID::random();

        $like = new Like(
            uuid: $newLikeUuid,
            post_uuid: new UUID($postUuid),
            user_uuid: $author->uuid(),

        );

        $this->likesRepository->save($like);

        return new SuccessFulResponse(
            ['uuid' => (string)$newLikeUuid]
        );
    }
}
