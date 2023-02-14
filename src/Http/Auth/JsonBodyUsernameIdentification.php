<?php
namespace Journal\Blog\Http\Auth;

use Journal\Blog\Exceptions\HttpException;
use Journal\Blog\Exceptions\UserNotFoundException;
use Journal\Blog\Repositories\UserRepository\UserRepositoryInterface;
use Journal\Blog\User;
use Journal\Http\Auth\AuthException;
use Journal\Http\Auth\IdentificationInterface;
use Journal\Http\Request;

class JsonBodyUsernameIdentification implements IdentificationInterface
{
    public function __construct(
        private UserRepositoryInterface $usersRepository
    )
    {
    }
    public function user(Request $request): User
    {
        try {
            $username = $request->jsonBodyField('username');
        } catch (HttpException $e) {

            throw new AuthException($e->getMessage());
        }
        try {
            return $this->usersRepository->getByUsername($username);
        } catch (UserNotFoundException $e) {
            throw new AuthException($e->getMessage());
        }
    }
}