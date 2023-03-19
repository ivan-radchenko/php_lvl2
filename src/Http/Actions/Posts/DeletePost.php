<?php

namespace Ivan\Php\Http\Actions\Posts;

use Ivan\Php\Blog\UUID;
use Ivan\Php\http\Request;
use Ivan\Php\http\Response;
use Ivan\Php\Http\ErrorResponse;
use Ivan\Php\Http\SuccessfulResponse;
use Ivan\Php\Http\Actions\ActionInterface;
use Ivan\Php\Blog\Exceptions\AuthException;
use Ivan\Php\Blog\Exceptions\PostNotFoundException;
use Ivan\Php\Http\Auth\TokenAuthenticationInterface;
use Ivan\Php\Blog\Repositories\PostsRepository\PostsRepositoryInterface;

class DeletePost implements ActionInterface
{
    public function __construct(
        private PostsRepositoryInterface $postsRepository,
        private TokenAuthenticationInterface $authentication,
    ) {
    }


    public function handle(Request $request): Response
    {
        try {
            $this->authentication->user($request);
        } catch (AuthException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        try {
            $postUuid = $request->query('uuid');
            $this->postsRepository->get(new UUID($postUuid));
        } catch (PostNotFoundException $error) {
            return new ErrorResponse($error->getMessage());
        }

        $this->postsRepository->delete(new UUID($postUuid));

        return new SuccessfulResponse([
            'uuid' => $postUuid,
        ]);
    }
}
