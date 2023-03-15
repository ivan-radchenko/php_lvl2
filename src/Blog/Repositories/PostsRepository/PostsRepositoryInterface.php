<?php

namespace Ivan\Php\Blog\Repositories\PostsRepository;

use Ivan\Php\Blog\Post;
use Ivan\Php\Blog\UUID;

interface PostsRepositoryInterface
{
    public function save(Post $post): void;
    public function get(UUID $uuid): Post;
    public function delete(UUID $uuid): void;
}