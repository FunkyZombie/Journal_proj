<?php

use Journal\Blog\Commands\Users\CreateUser;
use Journal\Blog\UnitTests\DummyLogger;
use Journal\Blog\Commands\Arguments;
use Journal\Blog\Commands\CreateUserCommand;
use Journal\Blog\Exceptions\InvalidArgumentException;
use Journal\Blog\Exceptions\UserNotFoundException;
use Journal\Blog\Repositories\UserRepository\UserRepositoryInterface;
use Journal\Blog\User;
use Journal\Blog\UUID;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

use PHPUnit\Framework\TestCase;

class CreateUserCommandTest extends TestCase
{
    private function makeUsersRepository(): UserRepositoryInterface
    {
        return new class implements UserRepositoryInterface {
            public function save(User $user): void
            {
            }

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
        $command = new CreateUser(
            $this->makeUsersRepository(),
        );

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'Not enough arguments (missing: "first_name, last_name").'
        );

        $command->run(
            new ArrayInput([
                'username' => 'Ivan',
                'password' => 'some_password',
            ]),
            new NullOutput()
        );
    }

    public function testItRequiresPassword(): void
    {
        $command = new CreateUser(
            $this->makeUsersRepository()
        );

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'Not enough arguments (missing: "password, first_name, last_name"'
        );

        $command->run(
            new ArrayInput([
                'username' => 'Ivan',
            ]),
            new NullOutput()
        );
    }

    public function testItSavesUserToRepository(): void
    {
        $usersRepository = $this->usersRepository();

        $command = new CreateUser(
            $usersRepository
        );

        $command->run(
            new ArrayInput([
                'username' => 'Ivan',
                'password' => 'some_password',
                'first_name' => 'Ivan',
                'last_name' => 'Nikitin',
            ]),
            new NullOutput()
        );

        $this->assertTrue($usersRepository->wasCalled());
    }

    private function usersRepository(): UserRepositoryInterface
    {
        return new class implements UserRepositoryInterface {

            private bool $called = false;

            public function __construct()
            {
            }

            public function save(User $user): void
            {
                $this->called = true;
            }
            public function get(UUID $uuid): User
            {
                throw new UserNotFoundException;
            }
            public function getByUsername(string $username): User
            {
                throw new UserNotFoundException;
            }

            public function wasCalled(): bool
            {
                return $this->called;
            }
        };
    }
}