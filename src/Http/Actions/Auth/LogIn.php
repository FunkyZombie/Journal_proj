<?php
namespace Journal\Http\Actions\Auth;

use DateTimeImmutable;

use Journal\Blog\AuthToken;
use Journal\Blog\Repositories\AuthTokenRepository\AuthTokensRepositoryInterface;
use Journal\Http\Actions\ActionInterface;
use Journal\Http\Auth\AuthException;
use Journal\Http\Auth\PasswordAuthenticationInterface;
use Journal\Http\ErrorResponse;
use Journal\Http\Request;
use Journal\Http\Response;
use Journal\Http\SuccessfulResponse;

class LogIn implements ActionInterface
{
    public function __construct(
        private PasswordAuthenticationInterface $passwordAuthentication,
        private AuthTokensRepositoryInterface $authTokensRepository
    )
    {
    }
    public function handle(Request $request): Response
    {
        try {
            $user = $this->passwordAuthentication->user($request);
        } catch (AuthException $e) {
            return new ErrorResponse($e->getMessage());
        }
        
        $authToken = new AuthToken(
            bin2hex(random_bytes(40)),
            $user->uuid(),
            (new DateTimeImmutable())->modify('+1 day')
        );
        
        $this->authTokensRepository->save($authToken);
        return new SuccessfulResponse([
            'token' => (string) $authToken->token()
        ]);
    }
}