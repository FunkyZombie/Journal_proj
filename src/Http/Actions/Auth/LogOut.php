<?php 
namespace Journal\Http\Actions\Auth;

use Journal\Blog\Exceptions\HttpException;
use Journal\Blog\Repositories\AuthTokenRepository\AuthTokensRepositoryInterface;
use Journal\Http\Actions\ActionInterface;
use Journal\Http\Auth\AuthException;
use Journal\Blog\Exceptions\AuthException\AuthTokenNotFoundException;
use Journal\Http\Auth\PasswordAuthenticationInterface;
use Journal\Http\Request;
use Journal\Http\Response;
use Journal\Http\SuccessfulResponse;

class LogOut implements ActionInterface
{
    private const HEADER_PREFIX = 'Bearer ';
    public function __construct(
        private AuthTokensRepositoryInterface $authTokensRepository
    )
    {
    }
    public function handle(Request $request): Response
    {
        try {
            $header = $request->header('Authorization');
        } catch (HttpException $e) {
            throw new AuthException($e->getMessage());
        }
        
        if (!str_starts_with($header, self::HEADER_PREFIX)) {
            throw new AuthException("Malformed token: [$header]");
        }
        
        $token = mb_substr($header, strlen(self::HEADER_PREFIX));
    
        try {
            $authToken = $this->authTokensRepository->get($token);
        } catch (AuthTokenNotFoundException) {
            throw new AuthException("Bad token: [$token]");
        }
        
        $this->authTokensRepository->update($authToken);
        
        return new SuccessfulResponse([
            'token' => (string) $authToken->token()
        ]);
    }
}