<?php

namespace Ivan\Php\Blog\Repositories\LikesRepository;

use Ivan\Php\Blog\Like;
use Ivan\Php\Blog\UUID;

interface LikesRepositoryInterface
{
    public function save(Like $like): void;
    public function getByPostUuid(UUID $uuid): array;
    public function checkUserLikeForPostExists(UUID $postUuid, UUID $userUuid): void;
}
