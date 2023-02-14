<?php

namespace Journal\Blog\Commands;

use Journal\Blog\Repositories\UserRepository\UserRepositoryInterface;

use Journal\Blog\Exceptions\UserNotFoundException;
use Journal\Blog\Exceptions\CommandException;

use Journal\Blog\User as User;
use Journal\Blog\Name as Name;
use Journal\Blog\UUID as UUID;
use Psr\Log\LoggerInterface;

class CreateUserCommand
{
    public function __construct(
        private UserRepositoryInterface $usersRepository,
        private LoggerInterface $logger
    ){}

    public function handler(Arguments $arguments): void
    {
        $this->logger->info('Create user commant started');
        
        $username = $arguments->get('username');

        if ($this->userExists($username)) {
            $this->logger->warning("User already exists: $username");
        }
        
        $uuid = UUID::random();

        $this->usersRepository->save(new User(
            uuid: $uuid,
            username: $username,
            name: new Name(
                first_name: $arguments->get('first_name'), 
                last_name: $arguments->get('last_name'))
        ));
        
        $this->logger->info("User created: $uuid");
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