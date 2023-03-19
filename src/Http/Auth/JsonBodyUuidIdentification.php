<?php

namespace Ivan\Php\Http\Auth;

use Ivan\Php\Blog\Exceptions\AuthException;
use Ivan\Php\Blog\Exceptions\HttpException;
use Ivan\Php\Blog\Exceptions\InvalidArgumentException;
use Ivan\Php\Blog\Exceptions\UserNotFoundException;
use Ivan\Php\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use Ivan\Php\Blog\User;
use Ivan\Php\Blog\UUID;
use Ivan\Php\Http\Request;

class JsonBodyUuidIdentification implements IdentificationInterface
{
    public function __construct(
        private UsersRepositoryInterface $usersRepository
    ) {
    }

    /**
     * @throws AuthException
     */
    public function user(Request $request): User
    {
        try {
            // Получаем UUID пользователя из JSON-тела запроса;
            // ожидаем, что корректный UUID находится в поле user_uuid
            $userUuid = new UUID($request->jsonBodyField('user_uuid'));
        } catch (HttpException | InvalidArgumentException $e) {
            // Если невозможно получить UUID из запроса -
            // бросаем исключение
            throw new AuthException($e->getMessage());
        }
        try {
            // Ищем пользователя в репозитории и возвращаем его
            return $this->usersRepository->get($userUuid);
        } catch (UserNotFoundException $e) {
            // Если пользователь с таким UUID не найден -
            // бросаем исключение
            throw new AuthException($e->getMessage());
        }
    }
}
