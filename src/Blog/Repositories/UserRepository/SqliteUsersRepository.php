<?php

namespace Journal\Blog\Repositories\UserRepository;

use Journal\Blog\Exceptions\UserNotFoundException;

use Journal\Blog\User;
use Journal\Blog\Name;
use Journal\Blog\UUID;
use PDO;
use PDOStatement;

class SqliteUsersRepository implements UserRepositoryInterface
{
    public function __construct(
        private PDO $connection
    )
    {
    }

    public function save(User $user): void
    {
        $statement = $this->connection->prepare(
            'INSERT INTO users (
                uuid, 
                username, 
                password,  
                first_name, 
                last_name
            )
            VALUES (
                :uuid, 
                :username, 
                :password, 
                :first_name, 
                :last_name
            ) ON CONFLICT (uuid) DO UPDATE SET
                first_name = :first_name,
                last_name = :last_name'
        );

        $statement->execute([
            ':uuid' => (string) $user->uuid(),
            ':username' => $user->username(),
            ':password' => $user->hashedPassword(),
            ':first_name' => $user->name()->firstName(),
            ':last_name' => $user->name()->lastName()
        ]);
    }

    public function get(UUID $uuid): User
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM users WHERE uuid = :uuid'
        );

        $statement->execute([
            ':uuid' => $uuid
        ]);

        return $this->getUser($statement, $uuid);
    }

    public function getByUsername(string $username): User
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM users WHERE username = :username'
        );

        $statement->execute([
            ':username' => $username,
        ]);

        return $this->getUser($statement, $username);
    }

    private function getUser(PDOStatement $statement, string $username): User
    {
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if (false === $result) {
            throw new UserNotFoundException(
                "Cannot find user: $username"
            );
        }

        return new User(
            new UUID($result['uuid']),
            $result['username'],
            $result['password'],
            new Name(
                $result['first_name'],
                $result['last_name']
            )
        );
    }
}