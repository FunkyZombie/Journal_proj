<?php

namespace Journal\Blog;

class User 
{
    function __construct(
        private UUID $uuid, 
        private string $username,
        private Name $name)
    {}
    public function name(): Name
    {
        return $this->name;
    }
    public function uuid(): UUID
    {
        return $this->uuid;
    }
    public function username(): string 
    {
        return $this->username;
    }
    public function __toString(): string
    {
        return $this->name . "\n";
    }
}