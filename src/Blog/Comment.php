<?php

namespace Journal\Blog;

class Comment
{
    function __construct(
        private UUID $uuid,
        private UUID $author_uuid,
        private UUID $post_uuid,
        private string $text
    ){}

    public function __toString(): string
    {
        return $this->author_uuid . ' пишет: ' . $this->text;
    }
    public function uuid(): UUID
    {
        return $this->uuid;
    }
    public function authorUUID(): UUID
    {
        return $this->author_uuid;
    }
    public function postUUID(): UUID
    {
        return $this->post_uuid;
    }
    public function text(): string
    {
        return $this->text;
    }
}