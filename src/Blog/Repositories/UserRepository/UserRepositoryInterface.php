<?php

namespace Journal\Blog\Repositories\UserRepository;

use Journal\Blog\User;
use Journal\Blog\UUID;

interface UserRepositoryInterface 
{
    public function save(User $user): void;
    public function get(UUID $uuid): User;
    public function getByUsername(string $username): User;
}