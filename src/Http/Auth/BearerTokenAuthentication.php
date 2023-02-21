<?php
namespace Journal\Http\Auth;

use DateTimeImmutable;
use Journal\Blog\Exceptions\AuthException\AuthTokenNotFoundException;
use Journal\Blog\Exceptions\HttpException;
use Journal\Blog\Repositories\AuthTokenRepository\AuthTokensRepositoryInterface;
use Journal\Blog\Repositories\UserRepository\UserRepositoryInterface;
use Journal\Blog\User;
use Journal\Http\Request;

class BearerTokenAuthentication implements TokenAuthenticationInterface
{
    private const HEADER_PREFIX = 'Bearer ';
    public function __construct(
        private AuthTokensRepositoryInterface $authTokensRepository,
        private UserRepositoryInterface $usersRepository,
    )
    {
    }
    public function user(Request $request): User
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
        
        if ($authToken->expiresOn() <= new DateTimeImmutable()) {
            throw new AuthException("Token expired: [$token]");
        }
        
        $userUuid = $authToken->userUuid();
        
        return $this->usersRepository->get($userUuid);
    }
}