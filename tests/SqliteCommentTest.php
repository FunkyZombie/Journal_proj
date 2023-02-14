<?php

use Journal\Blog\Exceptions\CommentNotFoundException;
use Journal\Blog\Comment;
use Journal\Blog\Repositories\PostRepository\SqliteCommentsRepository;
use Journal\Blog\UUID;

use PHPUnit\Framework\TestCase;

class SqliteCommentTest extends TestCase
{
    public function testItThrowAnExceptionWhenCommentNotFound(): void
    {
        $connectionStub = $this->createStub(PDO::class);

        $statementStub = $this->createStub(PDOStatement::class);

        $statementStub->method('fetch')->willReturn(false);

        $connectionStub->method('prepare')->willReturn($statementStub);

        $repository = new SqliteCommentsRepository($connectionStub);

        $this->expectException(CommentNotFoundException::class);
        $this->expectExceptionMessage('Comment not found');

        $repository->get(new UUID('95a3d8f4-cfb6-4e8c-ac07-899a1e52eb05'));
    }

    public function testItSavesCommentToDatabase(): void
    {
        $connectionStub = $this->createStub(PDO::class);
        $statementMock = $this->createMock(PDOStatement::class);
        $statementMock
            ->expects($this->once())
            ->method('execute')
            ->with([
                ':uuid' => '95a3d8f4-cfb6-4e8c-ac07-899a1e52eb05',
                ':author_uuid' => '123e4567-e89b-12d3-a456-426614174000',
                ':post_uuid' => '166d6f00-ecb5-4143-a57f-cd13f0e36fa4',
                ':text' => 'Text',
            ]);

        $connectionStub->method('prepare')->willReturn($statementMock);
        $repository = new SqliteCommentsRepository($connectionStub);

        $repository->save(new Comment(
                new UUID('95a3d8f4-cfb6-4e8c-ac07-899a1e52eb05'),
                new UUID('123e4567-e89b-12d3-a456-426614174000'),
                new UUID('166d6f00-ecb5-4143-a57f-cd13f0e36fa4'),
                'Text'
            )
        );
    }

    public function testItGetByUuid(): void 
    {
        $connectionStub = $this->createStub(PDO::class);
        $statementMock = $this->createMock(PDOStatement::class);

        $statementMock->method('fetch')->willReturn([
            'uuid' => '95a3d8f4-cfb6-4e8c-ac07-899a1e52eb05',
            'author_uuid' => '123e4567-e89b-12d3-a456-426614174000',
            'post_uuid' => '166d6f00-ecb5-4143-a57f-cd13f0e36fa4',
            'text' => 'Text',
        ]);

        $connectionStub->method('prepare')->willReturn($statementMock);

        $commentRepository = new SqliteCommentsRepository($connectionStub);
        $comment = $commentRepository->get(new UUID('166d6f00-ecb5-4143-a57f-cd13f0e36fa4'));

        $this->assertSame('166d6f00-ecb5-4143-a57f-cd13f0e36fa4', (string)$comment->postUUID());
    }
}      