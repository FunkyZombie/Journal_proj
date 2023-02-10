<?php

namespace Journal\Blog\Repositories\PostRepository;

use Journal\Blog\{Post, UUID};
use Journal\Blog\Exceptions\PostNotFoundException;
use Journal\Blog\Repositories\UserRepository\SqliteUsersRepository;
use PDO;
use PDOStatement;

class SqlitePostsRepository implements PostRepositoryInterface
{
    public function __construct(
        private PDO $connection,
        private SqliteUsersRepository $userRepository
    ) {}
    public function save(Post $post): void
    {
        $statement = $this->connection->prepare(
            'INSERT INTO posts (uuid, author, title, text)
            VALUES (:uuid, :author, :title, :text)'
        );

        $statement->execute([
            ':uuid' => (string)$post->uuid(),
            ':author' => (string)$post->author()->uuid(),
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

        return $this->getPost($statement);
    }

    private function getPost(PDOStatement $statement): Post
    {
        $result = $statement->fetch(PDO::FETCH_ASSOC);
    
        if ($result === false) {
            throw new PostNotFoundException('Post not found');
        }

        $user = $this->userRepository->get(new UUID($result['author']));

        return new Post(
            new UUID($result['uuid']),
            $user,
            $result['title'],
            $result['text']
        );
    }
    
    public function delete(UUID $uuid):void
    {
        $statement = $this->connection->prepare(
            'DELETE FROM posts WHERE posts.uuid=:uuid;'
        );
        
        $statement->execute([
            ':uuid' => $uuid,
        ]);
    }
}