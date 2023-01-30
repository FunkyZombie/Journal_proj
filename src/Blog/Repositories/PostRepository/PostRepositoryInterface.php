<?php

namespace Journal\Blog\Repositories\PostRepository;

use Journal\Blog\Post;
use Journal\Blog\UUID;

interface PostRepositoryInterface 
{
    public function save(Post $post): void;
    // public function get(UUID $uuid): Post;
}