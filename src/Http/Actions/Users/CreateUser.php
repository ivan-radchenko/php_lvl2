<?php

namespace Ivan\Php\Http\Actions\Users;

use Ivan\Php\Blog\Exceptions\HttpException;
use Ivan\Php\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use Ivan\Php\Blog\User;
use Ivan\Php\Blog\UUID;
use Ivan\Php\http\Actions\ActionInterface;
use Ivan\Php\http\ErrorResponse;
use Ivan\Php\http\Request;
use Ivan\Php\http\Response;
use Ivan\Php\http\SuccessfulResponse;
use Ivan\Php\Person\Name;

class CreateUser implements ActionInterface
{
    public function __construct(
        private UsersRepositoryInterface $usersRepository,
    ) {
    }

    public function handle(Request $request): Response
    {
        try {
            $newUserUuid = UUID::random();

            $user = new User(
                $newUserUuid,
                new Name(
                    $request->jsonBodyField('first_name'),
                    $request->jsonBodyField('last_name')
                ),
                $request->jsonBodyField('username')
            );

        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());

        }

        $this->usersRepository->save($user);

        return new SuccessfulResponse([
            'uuid' => (string)$newUserUuid,
        ]);
    }
}