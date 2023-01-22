<?php

namespace Journal\Users;

class User {
    private string $id;
    private string $first_name;
    private string $last_name;

    function __construct(string $id, string $first_name, string $last_name)
    {
        $this->id = $id;
        $this->first_name = $first_name;
        $this->last_name = $last_name;
    }

    public function __toString(): string
    {
        return "Пользователь: $this->first_name $this->last_name";
    }
}