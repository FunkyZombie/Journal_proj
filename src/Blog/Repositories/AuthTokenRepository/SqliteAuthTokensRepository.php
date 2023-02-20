<?php

namespace Journal\Blog\Repositories\AuthTokenRepository;

use DateTimeImmutable;
use DateTimeInterface;
use Exception;
use PDO;
use PDOException;

use Journal\Blog\AuthToken;
use Journal\Blog\Exceptions\AuthException\AuthTokenNotFoundException;
use Journal\Blog\Exceptions\AuthException\AuthTokensRepositoryException;
use Journal\Blog\UUID;


class SqliteAuthTokensRepository implements AuthTokensRepositoryInterface
{
    public function __construct(
        private PDO $connection
    )
    {
    }
    public function save(AuthToken $authToken): void
    {
        $query = <<<'SQL'
            INSERT INTO tokens (
                token,
                user_uuid,
                expires_on
            ) VALUES (
                :token,
                :user_uuid,
                :expires_on
            ) ON CONFLICT (token) DO UPDATE SET expires_on = :expires_on
            SQL;
            
        try {
            $statement = $this->connection->prepare($query);
            
            $statement->execute([
                ':token' => (string) $authToken->token(),
                ':user_uuid' => (string) $authToken->userUuid(),
                ':expires_on' => $authToken->expiresOn()
                    ->format(DateTimeInterface::ATOM),
            ]);
            
        } catch (PDOException $e) {
            throw new AuthTokensRepositoryException(
                $e->getMessage(),
                (int) $e->getCode(),
                $e
            );
        }
    }
    
    public function update(AuthToken $authToken):void
    {
        $query = <<<'SQL'
            UPDATE tokens SET expires_on = :expires_on 
            WHERE user_uuid = :user_uuid
            SQL;
            
        try {
            $statement = $this->connection->prepare($query);
            
            $statement->execute([
                ':user_uuid' => (string) $authToken->userUuid(),
                ':expires_on' => (new DateTimeImmutable())->modify('now')
                    ->format(DateTimeInterface::ATOM),
            ]);
            
        } catch (PDOException $e) {
            throw new AuthTokensRepositoryException(
                $e->getMessage(),
                (int) $e->getCode(),
                $e
            );
        }
    }
    public function get(string $token): AuthToken
    {
        try {
            $statement = $this->connection->prepare(
                'SELECT * FROM tokens WHERE token = ?'
            );
            $statement->execute([$token]);
            $result = $statement->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new AuthTokensRepositoryException(
                $e->getMessage(),
                (int) $e->getCode(),
                $e
            );
        }
        if (false === $result) {
            throw new AuthTokenNotFoundException("Cannot find token: $token");
        }
        try {
            return new AuthToken(
                $result['token'],
                new UUID($result['user_uuid']),
                new DateTimeImmutable($result['expires_on'])
            );
        } catch (Exception $e) {
            throw new AuthTokensRepositoryException(
                $e->getMessage(), $e->getCode(),
                $e
            );
        }
    }
}