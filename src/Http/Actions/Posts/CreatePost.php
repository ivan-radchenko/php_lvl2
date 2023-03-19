<?php

namespace Ivan\Php\Http\Actions\Posts;

use Psr\Log\LoggerInterface;
use Ivan\Php\Blog\Post;
use Ivan\Php\Blog\UUID;
use Ivan\Php\Http\Request;
use Ivan\Php\Http\Response;
use Ivan\Php\Http\ErrorResponse;
use Ivan\Php\http\SuccessfulResponse;
use Ivan\Php\Http\Actions\ActionInterface;
use Ivan\Php\Blog\Exceptions\AuthException;
use Ivan\Php\Blog\Exceptions\HttpException;
use Ivan\Php\Http\Auth\TokenAuthenticationInterface;
use Ivan\Php\Blog\Exceptions\InvalidArgumentException;
use Ivan\Php\Blog\Repositories\PostsRepository\PostsRepositoryInterface;

class CreatePost implements ActionInterface
{
    public function __construct(
        private PostsRepositoryInterface $postsRepository,
        // Внедряем контракт логгера
        private LoggerInterface $logger,
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
        } catch (AuthException $e) {
            return new ErrorResponse($e->getMessage());
        }


        $newPostUuid = UUID::random();

        try {
            $post = new Post(
                $newPostUuid,
                $author,
                $request->jsonBodyField('title'),
                $request->jsonBodyField('text'),
            );
        } catch (HttpException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        $this->postsRepository->save($post);
        $this->logger->info("Post created: $newPostUuid");

        return new SuccessfulResponse([
            'uuid' => (string)$newPostUuid,
        ]);
    }
}
