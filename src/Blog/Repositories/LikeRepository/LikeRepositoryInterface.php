<?php

namespace Journal\Blog\Repositories\LikeRepository;

use Journal\Blog\Like;
use Journal\Blog\UUID;

interface LikeRepositoryInterface 
{
    public function save(Like $like): void;
    public function getByPostUuid(UUID $uuid): array;
    public function has(UUID $post_uuid, UUID $user_uuid): bool;
}