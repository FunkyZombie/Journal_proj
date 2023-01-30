<?php

namespace Journal\Blog;

class Name 
{
    function __construct(
        private string $first_name,
        private string $last_name
    ) {
        $this->first_name = $first_name;
        $this->last_name = $last_name;
    }

    public function __toString()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function firstName():string
    {
        return $this->first_name;
    }

    public function lastName():string
    {
        return $this->last_name;
    }
}