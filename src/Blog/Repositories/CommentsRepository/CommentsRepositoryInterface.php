<?php

namespace Ivan\Php\Blog\Repositories\CommentsRepository;

use Ivan\Php\Blog\Comment;
use Ivan\Php\Blog\UUID;

interface CommentsRepositoryInterface
{
 public  function save(Comment $comment): void;
 public  function get(UUID $uuid): Comment;
}