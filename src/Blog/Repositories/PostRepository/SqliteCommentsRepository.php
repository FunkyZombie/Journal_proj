<?php

namespace Journal\Blog\Repositories\PostRepository;

use Journal\Blog\{Comment, UUID};
use Journal\Blog\Exceptions\CommentNotFoundException;
use PDO;
use PDOStatement;
class SqliteCommentsRepository implements CommentRepositoryInterface
{
    public function __construct(
        private PDO $connection
    ) {}
    public function save(Comment $comment): void
    {
        $statement = $this->connection->prepare(
            'INSERT INTO comments (uuid, author_uuid, post_uuid, text)
            VALUES (:uuid, :author_uuid, :post_uuid, :text)'
        );

        $statement->execute([
            ':uuid' => (string)$comment->uuid(),
            ':author_uuid' => (string)$comment->authorUUID(),
            ':post_uuid' => (string)$comment->postUUID(),
            ':text' => $comment->text()
        ]);
    }

    public function get(UUID $post_uuid):Comment
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM comments WHERE post_uuid = :post_uuid'
        );
        
        $statement->execute([
            ':post_uuid' => $post_uuid
        ]);
        
        return $this->getComment($statement, $post_uuid);
    }
    
    private function getComment(PDOStatement $statement, string $uuid):Comment
    {
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if (false === $result) {
            throw new CommentNotFoundException(
                "Comment not found"
            );
        }
        
        return new Comment(
            new UUID($result['uuid']),
            new UUID($result['author_uuid']),
            new UUID($result['post_uuid']),
            $result['text']
        );
    }

    public function getAllCommentsOnPost(UUID $post_uuid):array
    {
        $result = [];

        $statement = $this->connection->prepare(
            'SELECT * FROM comments WHERE post_uuid = :post_uuid'
        );

        $statement->execute([
            ':post_uuid' => $post_uuid
        ]);

        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $result[] = new Comment(
                new UUID($row['uuid']),
                new UUID($row['author_uuid']),
                new UUID($row['post_uuid']),
                $row['text']
            );
        }

        return $result ?: null;
    }
}