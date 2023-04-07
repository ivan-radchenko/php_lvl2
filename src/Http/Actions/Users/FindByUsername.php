<?php

namespace Ivan\Php\Http\Actions\Users;

use Ivan\Php\Blog\Exceptions\HttpException;
use Ivan\Php\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use Ivan\Php\Blog\Exceptions\UserNotFoundException;
use Ivan\Php\Http\Actions\ActionInterface;
use Ivan\Php\http\ErrorResponse;
use Ivan\Php\http\Request;
use Ivan\Php\http\Response;
use Ivan\Php\http\SuccessfulResponse;

class FindByUsername implements ActionInterface
{
    // Нам понадобится репозиторий пользователей,
    // внедряем его контракт в качестве зависимости
    public function __construct(
        private UsersRepositoryInterface $usersRepository
    )
    {
    }



    public function handle(Request $request): Response
    {
        try {
        // Пытаемся получить искомое имя пользователя из запроса
            $username = $request->query('username');
        } catch (HttpException $e) {
        // Если в запросе нет параметра username -
        // возвращаем неуспешный ответ,
        // сообщение об ошибке берём из описания исключения
            return new ErrorResponse($e->getMessage());
        }


        try {
    // Пытаемся найти пользователя в репозитории
            $user = $this->usersRepository->getByUsername($username);
        } catch (UserNotFoundException $e) {
    // Если пользователь не найден -
    // возвращаем неуспешный ответ
            return new ErrorResponse($e->getMessage());
        }


    // Возвращаем успешный ответ
        return new SuccessfulResponse([
            'username' => $user->username(),
            'name' => $user->name()->first() . ' ' . $user->name()->last(),
        ]);
    }
}