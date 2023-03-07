<?php

namespace Ivan\Php\Http\Actions\Posts;

use Ivan\Php\Blog\Exceptions\PostNotFoundException;
use Ivan\Php\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use Ivan\Php\Blog\UUID;
use Ivan\Php\Http\Actions\ActionInterface;
use Ivan\Php\Http\ErrorResponse;
use Ivan\Php\Http\SuccessfulResponse;
use Ivan\Php\http\Request;
use Ivan\Php\http\Response;

class DeletePost implements ActionInterface
{
    public function __construct(
        private PostsRepositoryInterface $postsRepository
    )
    {
    }


    public function handle(Request $request): Response
    {
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