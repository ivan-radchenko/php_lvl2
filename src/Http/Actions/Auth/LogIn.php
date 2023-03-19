<?php

namespace Ivan\Php\Http\Actions\Auth;

use DateTimeImmutable;
use Ivan\Php\Blog\AuthToken;
use Ivan\Php\Blog\Exceptions\AuthException;
use Ivan\Php\Blog\Repositories\AuthTokensRepository\AuthTokensRepositoryInterface;
use Ivan\Php\Http\Actions\ActionInterface;
use Ivan\Php\Http\Auth\PasswordAuthenticationInterface;
use Ivan\Php\http\Request;
use Ivan\Php\Http\ErrorResponse;
use Ivan\Php\http\Response;
use Ivan\Php\Http\SuccessfulResponse;

class LogIn implements ActionInterface
{
    public function __construct(
        // Авторизация по паролю
        private PasswordAuthenticationInterface $passwordAuthentication,
        // Репозиторий токенов
        private AuthTokensRepositoryInterface $authTokensRepository
    ) {
    }

    public function handle(Request $request): Response
    {
        // Аутентифицируем пользователя
        try {
            $user = $this->passwordAuthentication->user($request);
        } catch (AuthException $e) {
            return new ErrorResponse($e->getMessage());
        }
        // Генерируем токен
        $authToken = new AuthToken(
            // Случайная строка длиной 40 символов
            bin2hex(random_bytes(40)),
            $user->uuid(),
            // Срок годности - 1 день
            (new DateTimeImmutable())->modify('+1 day')
        );
        // Сохраняем токен в репозиторий
        $this->authTokensRepository->save($authToken);
        // Возвращаем токен
        return new SuccessfulResponse([
            'token' => $authToken->token(),
        ]);
    }
}
