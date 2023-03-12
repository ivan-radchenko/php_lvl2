<?php

namespace Ivan\Php\Http\Actions\Posts;

use Ivan\Php\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use Ivan\Php\Http\Actions\ActionInterface;
use Ivan\Php\http\Response;
use Ivan\Php\http\ErrorResponse;
use Ivan\Php\http\Request;
use Ivan\Php\Blog\Exceptions\HttpException;
use Ivan\Php\Blog\UUID;
use Ivan\Php\Blog\Exceptions\PostNotFoundException;
use Ivan\Php\http\SuccessfulResponse;



class FindByUuid implements ActionInterface
{
    // Нам понадобится репозиторий пользователей,
    // внедряем его контракт в качестве зависимости
    public function __construct(
        private PostsRepositoryInterface $postsRepository
    )
    {
    }



    public function handle(Request $request): Response
    {
        try {
        // Пытаемся получить искомое имя пользователя из запроса
            $postUuid = $request->query('uuid');
        } catch (HttpException $e) {
        // Если в запросе нет параметра username -
        // возвращаем неуспешный ответ,
        // сообщение об ошибке берём из описания исключения
            return new ErrorResponse($e->getMessage());
        }


        try {
    // Пытаемся найти пользователя в репозитории
            $post = $this->postsRepository->get(new UUID($postUuid));
        } catch (PostNotFoundException $e) {
    // Если пользователь не найден -
    // возвращаем неуспешный ответ
            return new ErrorResponse($e->getMessage());
        }


    // Возвращаем успешный ответ
        return new SuccessfulResponse([
            'uuid' => $post->uuid(),
            'post' => "user: ". $post->user()->username() . ', title: ' . $post->title() . ', text: ' . $post->text(),
        ]);
    }
}