<?php

namespace Journal\Blog\Commands;

use Journal\Blog\Repositories\UserRepository\UserRepositoryInterface;

use Journal\Blog\Exceptions\UserNotFoundException;
use Journal\Blog\Exceptions\CommandException;

use Journal\Blog\User as User;
use Journal\Blog\Name as Name;
use Journal\Blog\UUID as UUID;

class CreateUserCommand
{
    public function __construct(
        private UserRepositoryInterface $usersRepository
    ){}

    public function handler(Arguments $arguments): void
    {
        $username = $arguments->get('username');

        if ($this->userExists($username)) {
            throw new CommandException("User already exists: $username");
        }

        $this->usersRepository->save(new User(
            UUID::random(),
            $username,
            new Name($arguments->get('first_name'), $arguments->get('last_name'))
        ));
    }

    private function userExists(string $username): bool
    {
        try {
            // Пытаемся получить пользователя из репозитория
            $this->usersRepository->getByUsername($username);
        } catch (UserNotFoundException) {
            return false;
        }
        return true;
    }
}