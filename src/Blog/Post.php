<?php

namespace Journal\Blog;

class Post {
    function __construct(
            private UUID $uuid,
            private User $author,
            private string $title,
            private string $text
        )
    {}
    public function __toString(): string
    {
        return (string) $this->uuid;
    }
    public function uuid(): string
    {
        return (string) $this->uuid;
    }
    public function author():User
    {
        return $this->author;
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