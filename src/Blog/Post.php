<?php

namespace Journal\Blog;

class Post {
    function __construct(
            private UUID $uuid,
            private UUID $author_uuid,
            private string $title,
            private string $text
        )
    {}
    public function __toString(): string
    {
        return $this->author_uuid . ': ' . $this->title . ' >>> ' . $this->text;
    }
    public function uuid(): UUID
    {
        return $this->uuid;
    }
    public function authorUUID():UUID
    {
        return $this->author_uuid;
    }
    public function title():string
    {
        return $this->title;
    }
    public function text():string
    {
        return $this->text;
    }

}