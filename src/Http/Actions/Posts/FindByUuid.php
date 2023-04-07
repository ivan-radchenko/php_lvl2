<?php

namespace Ivan\Php\Http\Actions\Posts;

use Ivan\Php\Blog\UUID;
use Ivan\Php\http\Request;
use Ivan\Php\http\Response;
use Ivan\Php\http\ErrorResponse;
use Ivan\Php\http\SuccessfulResponse;
use Ivan\Php\Http\Actions\ActionInterface;
use Ivan\Php\Blog\Exceptions\AuthException;
use Ivan\Php\Blog\Exceptions\HttpException;
use Ivan\Php\Blog\Exceptions\PostNotFoundException;
use Ivan\Php\Http\Auth\TokenAuthenticationInterface;
use Ivan\Php\Blog\Repositories\PostsRepository\PostsRepositoryInterface;



class FindByUuid implements ActionInterface
{
    // Нам понадобится репозиторий пользователей,
    // внедряем его контракт в качестве зависимости
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
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $post = $this->postsRepository->get(new UUID($postUuid));
        } catch (PostNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }

        return new SuccessfulResponse([
            'uuid' => $post->uuid(),
            'post' => "user: " . $post->user()->username() . ', title: ' . $post->title() . ', text: ' . $post->text(),
        ]);
    }
}
