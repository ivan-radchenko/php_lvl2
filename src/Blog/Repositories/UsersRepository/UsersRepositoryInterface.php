<?php

namespace Ivan\Php\Blog\Repositories\UsersRepository;

use Ivan\Php\Blog\User;
use Ivan\Php\Blog\UUID;

interface UsersRepositoryInterface
{
    public function save(User $user): void;
    public function get(UUID $uuid): User;
    public function getByUsername(string $username): User;
}