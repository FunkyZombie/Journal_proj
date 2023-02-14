<?php

namespace Journal\Blog\Repositories\PostRepository;

use Journal\Blog\{Post, UUID, Comment};
use PDO;
use PDOStatement;

class SqlitePostsRepository implements PostRepositoryInterface
{
    public function __construct(
        private PDO $connection
    ) {}
    public function save(Post $post): void
    {
        $statement = $this->connection->prepare(
            'INSERT INTO posts (uuid, author_uuid, title, text)
            VALUES (:uuid, :author_uuid, :title, :text)'
        );

        $statement->execute([
            ':uuid' => (string)$post->uuid(),
            ':author_uuid' => (string)$post->authorUUID(),
            ':title' => (string)$post->title(),
            ':text' => $post->text()
        ]);
    }
    public function get(UUID $uuid): Post
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM posts WHERE uuid = :uuid'
        );
        
        $statement->execute([
            ':uuid' => $uuid
        ]);

        return $this->getPost($statement, $uuid);
    }
    private function getPost(PDOStatement $statement, string $uuid): Post
    {
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        // if (false === $result) {
        //     throw new UserNotFoundException(
        //         "Cannot find user: $username"
        //     );
        // }

        return new Post(
            new UUID($result['uuid']),
            new UUID($result['author_uuid']),
            $result['title'],
            $result['text']
        );
    }
    public function getAllPost(): array
    {
        $result = [];

        $statement = $this->connection->prepare(
            'SELECT * FROM posts'
        );

        $statement->execute([]);

        while ($row = $statement->fetchObject()) {
            $result[] = new Post(
                new UUID($row->uuid),
                new UUID($row->author_uuid),
                $row->title,
                $row->text
            );
        }

        return $result ?: null;
    }
}