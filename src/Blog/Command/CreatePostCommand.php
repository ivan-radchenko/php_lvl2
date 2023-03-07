<?php

namespace Ivan\Php\Blog\Command;

use Ivan\Php\Blog\Exceptions\ArgumentsException;
use Ivan\Php\Blog\Exceptions\CommandException;
use Ivan\Php\Blog\Exceptions\InvalidArgumentException;
use Ivan\Php\Blog\Exceptions\UserNotFoundException;
use Ivan\Php\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use Ivan\Php\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use Ivan\Php\Blog\Post;
use Ivan\Php\Blog\UUID;

//php cli.php post username=Den title=Viva text=SeLaVi

class CreatePostCommand
{


    public function __construct(
        private PostsRepositoryInterface $postsRepository,
        private UsersRepositoryInterface $usersRepository
    )
    {
    }

    public function handle(Arguments $arguments): void
    {
        $username = $arguments->get('username');

        if ($this->userExists($username)) {
           $user = $this->usersRepository->getByUsername($username);
        } else {
            throw new UserNotFoundException("User not found: $username");
        }
        
        $this->postsRepository->save(new Post(
            UUID::random(),
            $user,
            $arguments->get('title'),
            $arguments->get('text')
        ));
    }
    private function userExists(string $username): bool
    {
        try {       
            $this->usersRepository->getByUsername($username);
        } catch (UserNotFoundException) {
            return false;
        }
        return true;
    }
}