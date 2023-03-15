<?php

namespace Ivan\Php\Blog\Repositories\UsersRepository;

use Ivan\Php\Blog\User;
use Ivan\Php\Blog\UUID;
use Ivan\Php\Blog\Exceptions\UserNotFoundException;

class InMemoryUsersRepository implements UsersRepositoryInterface
{

    private array $users = [];


    public function save(User $user): void
    {
        $this->users[] = $user;
    }

    /**
     * @param int $id
     * @return User
     * @throws UserNotFoundException
     */
    public function get(UUID $id): User
    {
        foreach ($this->users as $user) {
            if ($user->uuid() === $id) {
                return $user;
            }
        }
        throw new UserNotFoundException("User not found: $id");
    }

    public function getByUsername(string $username): User
    {
        foreach ($this->users as $user) {
            if ($user->username() === $username) {
                return $user;
            }
        }

        throw new UserNotFoundException("User not found: $username");
    }
}
