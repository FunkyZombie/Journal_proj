<?php

namespace Journal\Blog;

use Journal\Blog\Exceptions\InvalidArgumentException;

class UUID
{
    function __construct(
        private string $uuidString
    )
    {
        if (!uuid_is_valid($uuidString)) {
            throw new InvalidArgumentException(
                "Malform UUID: $this->uuidString"
            );
        }
    }

    public static function random(): self
    {
        return new self(uuid_create(UUID_TYPE_RANDOM));
    }

    public function __toString(): string
    {
        return (string)$this->uuidString;
    }
}