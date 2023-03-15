<?php

namespace Ivan\Php\Blog\Command;

use Ivan\Php\Blog\Exceptions\ArgumentsException;
use Ivan\Php\Blog\Exceptions\CommandException;
use Ivan\Php\Blog\Exceptions\InvalidArgumentException;
use Ivan\Php\Blog\Repositories\CommentsRepository\CommentsRepositoryInterface;
use Ivan\Php\Blog\Comment;
use Ivan\Php\Blog\UUID;

//php cli.php comment post_uuid=59f095ce-8b43-487b-973c-d30aa00185f0 author_uuid=76a49ea6-7883-4475-8f0f-74e18f6ea524 text=SeLaVi

class CreateCommentCommand
{
    public function __construct(
        private CommentsRepositoryInterface $commentsRepository
    )
    {
    }

    public function handle(Arguments $arguments): void
    {        
        $this->commentsRepository->save(new Comment(
            UUID::random(),
            UUID::uuidFromString($arguments->get('post_uuid')),
            UUID::uuidFromString($arguments->get('author_uuid')),
            $arguments->get('text')
        ));
    }
}