<?php

use Journal\Blog\Commands\Arguments;
use Journal\Blog\Commands\CreateUserCommand;
use Journal\Blog\Exceptions\InvalidArgumentException;
use Journal\Blog\Exceptions\UserNotFoundException;
use Journal\Blog\Repositories\UserRepository\UserRepositoryInterface;
use Journal\Blog\User;
use Journal\Blog\UUID;

use PHPUnit\Framework\TestCase;

class CreateUserCommandTest extends TestCase
{
    private function makeUsersRepository(): UserRepositoryInterface
    {
        return new class implements UserRepositoryInterface {
            public function save(User $user): void {}
            
            public function get(UUID $uuid): User
            {
                throw new UserNotFoundException("Not found");
            }
            
            public function getByUsername(string $username): User
            {
                throw new UserNotFoundException("Not found");
            }
        };
    }
    public function testItRequiresFirstName(): void
    {
        // Вызываем ту же функцию
        $command = new CreateUserCommand($this->makeUsersRepository());
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('No such argument: first_name');
        $command->handler(new Arguments([
            'username' => 'Ivan',

            'last_name' => 'Nikitin',
        ]));
    }

    public function testItRequiresLastName(): void
    {
        // Вызываем ту же функцию
        $command = new CreateUserCommand($this->makeUsersRepository());
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('No such argument: last_name');
        $command->handler(new Arguments([
            'username' => 'Ivan',

            'first_name' => 'Ivan',
        ]));
    }
}