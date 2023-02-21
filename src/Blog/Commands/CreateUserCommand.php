<?php

namespace Journal\Blog\Commands;

use Journal\Blog\Repositories\UserRepository\UserRepositoryInterface;

use Journal\Blog\Exceptions\UserNotFoundException;

use Journal\Blog\User as User;
use Journal\Blog\Name as Name;
use Psr\Log\LoggerInterface;

class CreateUserCommand
{
    public function __construct(
        private UserRepositoryInterface $usersRepository,
        private LoggerInterface $logger
    ){}

    public function handler(Arguments $arguments): void
    {
        $this->logger->info("Create user command started");
        
        $username = $arguments->get('username');
        
        if ($this->userExists($username)) {
            $this->logger->warning("User already exists: $username");
            return;
        }
        
        $user = User::createFrom(
            $username,
            $arguments->get('password'),
            new Name(
                $arguments->get('first_name'),
                $arguments->get('last_name')
            )
        );

        $this->usersRepository->save($user);
        
        $this->logger->info('User created: ' . $user->uuid());
    }

    private function userExists(string $username): bool
    {
        try {
            $this->usersRepository->getByUsername($username);
        } catch (UserNotFoundException) {
            return false;
        }
        return true;
    }
}