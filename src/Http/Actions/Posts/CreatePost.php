<?php

namespace Ivan\Php\Http\Actions\Posts;

use Ivan\Php\Blog\Exceptions\InvalidArgumentException;
use Ivan\Php\Http\Actions\ActionInterface;
use Ivan\Php\Http\ErrorResponse;
use Ivan\Php\Blog\Exceptions\HttpException;
use Ivan\Php\Http\Request;
use Ivan\Php\Http\Response;
use Ivan\Php\http\SuccessfulResponse;
use Ivan\Php\Blog\Post;
use Ivan\Php\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use Ivan\Php\Blog\Exceptions\UserNotFoundException;
use Ivan\Php\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use Ivan\Php\Blog\UUID;

class CreatePost implements ActionInterface
{
// Внедряем репозитории статей и пользователей
public function __construct(
private PostsRepositoryInterface $postsRepository,
private UsersRepositoryInterface $usersRepository,
) {
}
public function handle(Request $request): Response
{
// Пытаемся создать UUID пользователя из данных запроса
try {
$authorUuid = new UUID($request->jsonBodyField('author_uuid'));
} catch (HttpException | InvalidArgumentException $e) {
return new ErrorResponse($e->getMessage());
}
// Пытаемся найти пользователя в репозитории
try {
$user = $this->usersRepository->get($authorUuid);
} catch (UserNotFoundException $e) {
return new ErrorResponse($e->getMessage());
}
// Генерируем UUID для новой статьи
$newPostUuid = UUID::random();
try {
// Пытаемся создать объект статьи
// из данных запроса
$post = new Post(
$newPostUuid,
$user,
$request->jsonBodyField('title'),
$request->jsonBodyField('text'),
);
} catch (HttpException $e) {
return new ErrorResponse($e->getMessage());
}
// Сохраняем новую статью в репозитории
$this->postsRepository->save($post);
// Возвращаем успешный ответ,
// содержащий UUID новой статьи
return new SuccessfulResponse([
'uuid' => (string)$newPostUuid,
]);
}
}