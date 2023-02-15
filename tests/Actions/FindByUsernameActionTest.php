<?php

/*
*  Запуск теста [ composer test-stderr ](vendor/phpunit/phpunit/phpunit tests --testdox --colors --stderr)
*/
namespace Journal\Blog\UnitTests\Actions;

use Journal\Blog\UnitTests\DummyLogger;
use Journal\Http\Actions\Users\FindByUsername;

use Journal\Http\ErrorResponse;
use Journal\Http\Request;
use Journal\Http\SuccessfulResponse;

use Journal\Blog\Name;
use Journal\Blog\User;
use Journal\Blog\UUID;

use Journal\Blog\Exceptions\UserNotFoundException;

use Journal\Blog\Repositories\UserRepository\UserRepositoryInterface;

use PHPUnit\Framework\TestCase;

class FindByUsernameActionTest extends TestCase
{
    public function testItReturnsErrorResponseIfNoUsernameProvided(): void
    {
        $request = new Request([], [], '');
        
        $usersRepository = $this->usersRepository([]);
        $action = new FindByUsername($usersRepository, new DummyLogger());
        $response = $action->handle($request);
        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->expectOutputString('{"success":false,"reason":"No such query param in the request: username"}');
        $response->send();
    }

    public function testItReturnsErrorResponseIfUserNotFound(): void
    {
        $request = new Request(['username' => 'ivan'], [], '');
        $usersRepository = $this->usersRepository([]);
        $action = new FindByUsername($usersRepository, new DummyLogger());
        $response = $action->handle($request);
        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->expectOutputString('{"success":false,"reason":"Not found"}');
        $response->send();
    }

    public function testItReturnsSuccessfulResponse(): void
    {
        $request = new Request(['username' => 'ivan'], [], '');
        $usersRepository = $this->usersRepository([
            new User(
                UUID::random(),
                'ivan',
                new Name('Ivan', 'Nikitin')
            ), 
        ]);
        $action = new FindByUsername($usersRepository, new DummyLogger());
        $response = $action->handle($request);
        $this->assertInstanceOf(SuccessfulResponse::class, $response);
        $this->expectOutputString('{"success":true,"data":{"username":"ivan","name":"Ivan Nikitin"}}');
        $response->send();
    }
    private function usersRepository(array $users): UserRepositoryInterface
    {
        return new class ($users) implements UserRepositoryInterface {
            public function __construct(
                private array $users
            )
            {
            }
            public function save(User $user): void
            {
            }
            public function get(UUID $uuid): User
            {
                throw new UserNotFoundException("Not found");
            }
            public function getByUsername(string $username): User
            {
                foreach ($this->users as $user) {
                    if ($user instanceof User && $username === $user->username()) {
                        return $user;
                    }
                }
                throw new UserNotFoundException("Not found");
            }
        };
    }
}