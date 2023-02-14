<?php

namespace Journal\Blog\Repositories\UserRepository;

use Journal\Blog\Exceptions\UserNotFoundException;
use Journal\Blog\User;
use Journal\Blog\UUID;

class InMemoryUsersRepository implements UserRepositoryInterface
{
    private array $users = [];

    public function save(User $user):void
    {
        $this->users[] = $user;
    }

    public function get(UUID $uuid): User
    {
        foreach ($this->users as $user) {
            if ((string)$user->uuid() === (string)$uuid) {
                return $user;
            }
        }
        throw new UserNotFoundException("User not found: $uuid\n");
    }

    public function getByUsername(string $username): User
    {
        foreach ($this->users as $user) {
            if ($user->username() === $username) {
                return $user;
            }
        }
        throw new UserNotFoundException("User not found: $username");
    }
}