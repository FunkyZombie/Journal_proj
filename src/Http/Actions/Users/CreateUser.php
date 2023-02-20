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
use Psr\Log\LoggerInterface;

class CreateUser implements ActionInterface
{
    public function __construct(
        private UserRepositoryInterface $usersRepository,
        private LoggerInterface $logger,
    )
    {
    }

    public function handle(Request $request): Response
    {
        $username = $request->jsonBodyField('username');
        try {
            $user = User::createFrom(
                $username,
                $request->jsonBodyField('password'),
                new Name(
                    $request->jsonBodyField('first_name'),
                    $request->jsonBodyField('last_name'),
                )
            );
        } catch (HttpException $e) {
            $this->logger->warning($e->getMessage());
            return new ErrorResponse($e->getMessage());
        }

        $this->usersRepository->save($user);

        $this->logger->info('Create user: ' . $username . " (id: {$user->uuid()}");
        
        return new SuccessfulResponse([
            'uuid' => (string) $user->uuid(),
        ]);
    }
}