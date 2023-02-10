<?php

namespace Journal\Http\Actions\Users;

use Journal\Blog\Exceptions\HttpException;
use Journal\Blog\Name;
use Journal\Blog\Repositories\UserRepository\UserRepositoryInterface;

use Journal\Blog\User;
use Journal\Blog\UUID;

use Journal\Http\Actions\ActionInterface;
use Journal\Http\ErrorResponse;
use Journal\Http\Request;
use Journal\Http\Response;
use Journal\Http\SuccessfulResponse;


class CreateUser implements ActionInterface
{
    public function __construct(
        private UserRepositoryInterface $usersRepository,
    ){}
    
    public function handle(Request $request): Response
    {
        $newUserUuid = UUID::random();
        try {
            $user = new User(
                $newUserUuid,
                $request->jsonBodyField('username'),
                new Name(
                    $request->jsonBodyField('first_name'),
                    $request->jsonBodyField('last_name'),
                )
            );
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $this->usersRepository->save($user);

        return new SuccessfulResponse([
            'uuid' => (string) $newUserUuid,
        ]);
    }
}
