<?php
namespace Journal\Http\Auth;

use Journal\Http\Auth\AuthenticationInterface;
use Journal\Blog\Exceptions\HttpException;
use Journal\Blog\Exceptions\InvalidArgumentException;
use Journal\Blog\Exceptions\UserNotFoundException;
use Journal\Blog\Repositories\UserRepository\UserRepositoryInterface;
use Journal\Blog\User;
use Journal\Blog\UUID;
use Journal\Http\Auth\AuthException;
use Journal\Http\Request;

class JsonBodyUuidIdentification implements AuthenticationInterface
{
    public function __construct(
        private UserRepositoryInterface $usersRepository
    )
    {
    }
    public function user(Request $request): User
    {
        try {
            $userUuid = new UUID($request->jsonBodyField('user_uuid'));
        } catch (HttpException | InvalidArgumentException $e) {
            throw new AuthException($e->getMessage());
        }
        try {
            return $this->usersRepository->get($userUuid);
        } catch (UserNotFoundException $e) {
            throw new AuthException($e->getMessage());
        }
    }
}