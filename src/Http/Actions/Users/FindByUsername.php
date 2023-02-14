<?php

namespace Journal\Http\Actions\Users;

use Journal\Blog\Exceptions\HttpException;
use Journal\Blog\Exceptions\UserNotFoundException;
use Journal\Blog\Repositories\UserRepository\UserRepositoryInterface;
use Journal\Http\Request;
use Journal\Http\Response;
use Journal\Http\ErrorResponse;
use Journal\Http\SuccessfulResponse;
use Journal\Http\Actions\ActionInterface;

class FindByUsername implements ActionInterface
{
    public function __construct(
        private UserRepositoryInterface $usersRepository
    )
    {}
    public function handle(Request $request): Response
    {
        try {
            $username = $request->query('username');
        } catch (HttpException $e) {

            return new ErrorResponse($e->getMessage());
        }
        
        try {
            $user = $this->usersRepository->getByUsername($username);
        } catch (UserNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }
        return new SuccessfulResponse([
            'username' => $user->username(),
            'name' => $user->name()->firstName() . ' ' . $user->name()->lastName(),
        ]);
    }
}