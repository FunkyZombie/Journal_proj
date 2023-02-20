<?php

namespace Journal\Http\Auth;

use Journal\Blog\Exceptions\HttpException;
use Journal\Blog\Exceptions\UserNotFoundException;
use Journal\Http\Auth\PasswordAuthenticationInterface;
use Journal\Blog\Repositories\UserRepository\UserRepositoryInterface;
use Journal\Blog\User;
use Journal\Http\Request;

class PasswordAuthentication implements PasswordAuthenticationInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    )
    {
    }
    /**
     * @param \Journal\Http\Request $request
     * @return \Journal\Blog\User
     */
    public function user(Request $request): User
    {
        try {
            $username = $request->jsonBodyField('username');
        } catch (HttpException $e) {
            throw new AuthException($e->getMessage());
        }

        try {
            $user = $this->userRepository->getByUsername($username);
        } catch (UserNotFoundException $e) {
            throw new AuthException($e->getMessage());
        }

        try {
            $password = $request->jsonBodyField('password');
        } catch (HttpException $e) {
            throw new AuthException($e->getMessage());
        }

        if (!$user->checkPassword($password)) {
            throw new AuthException('Wrong password');
        }

        return $user;
    }
}