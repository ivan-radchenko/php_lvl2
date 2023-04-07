<?php

namespace Ivan\Php\Http\Actions\Auth;

use DateTimeImmutable;
use Ivan\Php\Http\Request;
use Ivan\Php\Http\Response;
use Ivan\Php\Http\SuccessfulResponse;
use Ivan\Php\Http\Actions\ActionInterface;
use Ivan\Php\Blog\Exceptions\AuthException;
use Ivan\Php\Http\Auth\BearerTokenAuthentication;
use Ivan\Php\Blog\Exceptions\AuthTokenNotFoundException;
use Ivan\Php\Blog\Repositories\AuthTokensRepository\AuthTokensRepositoryInterface;

class LogOut implements ActionInterface
{

    public function __construct(
        private AuthTokensRepositoryInterface $authTokensRepository,
        private BearerTokenAuthentication $authentication
    ) {
    }

    /**
     * @throws AuthException
     */
    public function handle(Request $request): Response
    {
        $token = $this->authentication->getAuthTokenString($request);

        try {
            $authToken = $this->authTokensRepository->get($token);
        } catch (AuthTokenNotFoundException $exception) {
            throw new AuthException($exception->getMessage());
        }

        $authToken->setExpiresOn(new DateTimeImmutable("now"));


        $this->authTokensRepository->save($authToken);

        return new SuccessfulResponse([
            'token' => $authToken->token()
        ]);
    }
}
