<?php

use Journal\Blog\Exceptions\PostNotFoundException;
use Journal\Blog\Name;
use Journal\Blog\Post;
use Journal\Blog\Repositories\PostRepository\SqlitePostsRepository;
use Journal\Blog\Repositories\UserRepository\SqliteUsersRepository;
use Journal\Blog\User;
use Journal\Blog\UUID;
use PHPUnit\Framework\TestCase;

class SqlitePostRepositoryTest extends TestCase
{
    public function testItThrowAnExceptionWhenPostNotFound(): void
    {
        $connectionStub = $this->createStub(PDO::class);

        $statementStub = $this->createStub(PDOStatement::class);

        $statementStub->method('fetch')->willReturn(false);

        $connectionStub->method('prepare')->willReturn($statementStub);

        $repository = new SqlitePostsRepository($connectionStub, new SqliteUsersRepository($connectionStub));

        $this->expectException(PostNotFoundException::class);
        $this->expectExceptionMessage('Post not found');

        $repository->get(new UUID('166d6f00-ecb5-4143-a57f-cd13f0e36fa4'));
    }

    public function testItSavesPostToDatabase(): void
    {
        $connectionStub = $this->createStub(PDO::class);
        $statementMock = $this->createMock(PDOStatement::class);
        $statementMock
            ->expects($this->once())
            ->method('execute')
            ->with([
                ':uuid' => '166d6f00-ecb5-4143-a57f-cd13f0e36fa4',
                ':author' => '123e4567-e89b-12d3-a456-426614174000',
                ':title' => 'Title',
                ':text' => 'Text',
            ]);

        $connectionStub->method('prepare')->willReturn($statementMock);
        $repository = new SqlitePostsRepository($connectionStub, new SqliteUsersRepository($connectionStub));

        $repository->save(new Post(
                new UUID('166d6f00-ecb5-4143-a57f-cd13f0e36fa4'),
                new User(new UUID('123e4567-e89b-12d3-a456-426614174000'), 'Funky', new Name('Funky', 'Monk')),
                'Title',
                'Text'
            )
        );
    }

    public function testItGetByUuid(): void 
    {
        $connectionStub = $this->createStub(PDO::class);
        $statementMock = $this->createMock(PDOStatement::class);
        
        $connectionUserStub = $this->createStub(PDO::class);
        $statementUserMock = $this->createMock(PDOStatement::class);
        
        $statementUserMock->method('fetch')->willReturn([
            'uuid' => '5b1da8ae-9a21-45c2-9dcc-52189a966979',
            'username' => 'TestUser',
            'first_name' => 'First',
            'last_name' => 'Last'
        ]);
        
        $statementMock->method('fetch')->willReturn([
            'uuid' => '8e368fdb-c8b8-46f7-bb30-f2e59d3e1ff1',
            'author' => '5b1da8ae-9a21-45c2-9dcc-52189a966979',
            'title' => 'Title',
            'text' => 'Text'
        ]);
        
        $connectionUserStub->method('prepare')->willReturn($statementUserMock);
        $connectionStub->method('prepare')->willReturn($statementMock);

        $postRepository = new SqlitePostsRepository($connectionStub, new SqliteUsersRepository($connectionUserStub));
        $post = $postRepository->get(new UUID('8e368fdb-c8b8-46f7-bb30-f2e59d3e1ff1'));

        $this->assertSame('8e368fdb-c8b8-46f7-bb30-f2e59d3e1ff1', (string)$post->uuid());

    }
}       


