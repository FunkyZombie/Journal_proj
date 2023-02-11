<?php

namespace Journal\Blog\Repositories\LikeRepository;
use Journal\Blog\Like;
use Journal\Blog\UUID;
use PDO;

class SqliteLikeRepository implements LikeRepositoryInterface
{
    public function __construct(
        private PDO $connection
    ) {}
	public function save(Like $like): void 
    {
        $statement = $this->connection->prepare(
            'INSERT INTO like  (uuid, post_uuid, user_uuid)
            VALUES (:uuid, :post_uuid, :user_uuid)'
        );
        
        $statement->execute([
            ':uuid' => (string) $like->uuid(),
            ':post_uuid' => (string) $like->postUuid(),
            ':user_uuid' => (string) $like->userUuid()
        ]);
	}
	/**
	 *
	 * @param UUID $uuid
	 * @return Like
	 */
	public function getByPostUuid(UUID $uuid): array 
    {
        $result = [];

        $statement = $this->connection->prepare(
            'SELECT * FROM like WHERE post_uuid = :post_uuid'
        );

        $statement->execute([
            ':post_uuid' => $uuid
        ]);

        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $result[] = new Like(
                new UUID($row['uuid']),
                new UUID($row['post_uuid']),                    
                new UUID($row['user_uuid']),
            );
        }

        return $result ?: null;
	}
    
    public function has(UUID $post_uuid, UUID $user_uuid): bool
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM like 
            WHERE post_uuid = :post_uuid 
            AND user_uuid = :user_uuid'
        );
        
        $statement->execute([
            'post_uuid' => $post_uuid,
            'user_uuid' => $user_uuid
        ]);

        $result = $statement->fetch(PDO::FETCH_ASSOC);
        
        if (is_array($result)) {
            return false;
        }
        return true;
    }
}