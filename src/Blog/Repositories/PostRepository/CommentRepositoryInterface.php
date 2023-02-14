<?php

namespace Journal\Blog\Repositories\PostRepository;

use Journal\Blog\Comment;
use Journal\Blog\UUID;

interface CommentRepositoryInterface 
{
    public function save(Comment $comment): void;
    public function get(UUID $uuid): Comment;
}