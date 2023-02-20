<?php

namespace Journal\Blog\UnitTests\Actions;

use Journal\Blog\Comment;
use Journal\Blog\Exceptions\CommentNotFoundException;
use Journal\Blog\Exceptions\InvalidArgumentException;
use Journal\Blog\Exceptions\PostNotFoundException;
use Journal\Blog\Exceptions\UserNotFoundException;
use Journal\Blog\Post;
use Journal\Blog\Repositories\PostRepository\CommentRepositoryInterface;
use Journal\Blog\Repositories\PostRepository\PostRepositoryInterface;
use Journal\Blog\Repositories\UserRepository\UserRepositoryInterface;
use Journal\Blog\User;
use Journal\Blog\UUID;
use Journal\Http\Actions\Comments\CreateComment;
use Journal\Http\Auth\AuthenticationInterface;
use Journal\Http\Auth\TokenAuthenticationInterface;
use Journal\Http\ErrorResponse;
use Journal\Http\Request;
use Journal\Blog\Name;
use Journal\Http\SuccessfulResponse;
use PHPUnit\Framework\TestCase;

class CreateCommentActionTest extends TestCase
{
    private function postsRepository(array $posts): PostRepositoryInterface
    {
        return new class($posts) implements PostRepositoryInterface 
        {
            public function __construct(
                private array $posts
            ) {}
            
            private bool $called = false;
            
            public function save(Post $post):void
            {
                $this->called = true;
            }

            public function get(UUID $uuid): Post
            {
                foreach ($this->posts as $post) {
                    if ($post instanceof Post && (string)$uuid == $post->uuid()) {
                        return $post;
                    }
                }
                throw new PostNotFoundException('Post not found');
            }

            public function getCalled(): bool
            {
                return $this->called;
            }
            
            public function delete(UUID $uuid): void 
            {
            }
        };
    }
    
    private function usersRepository(array $users): UserRepositoryInterface
    {
        return new class($users) implements UserRepositoryInterface
        { 
            public function __construct(
                private array $users
            ) {}
            
            public function save(User $user): void 
            {}
            
            public function get(UUID $uuid): User
            {
                foreach ($this->users as $user) {
                    if ($user instanceof User && (string)$uuid == $user->uuid()) {
                        return $user;
                    }
                }
                throw new UserNotFoundException('Cannot find user: ' . $uuid);
            }
            
            public function getByUsername(string $username): User
            {
                throw new UserNotFoundException('Not found');
            }
        };
    }
    
    private function commentRepository(): CommentRepositoryInterface
    {
        return new class() implements CommentRepositoryInterface
        {
            private bool $called = false;
            
            public function save(Comment $comment): void 
            {
                $this->called = true;
            }
            
            public function get(UUID $uuid): Comment
            {
                throw new CommentNotFoundException('Comment not found');
            }
            
            public function getCalled(): bool
            {
                return $this->called;
            }
        };  
    }
    
    
    public function testItReturnsSuccessfulResponse(): void
    {
        $request = new Request([], [], '{"author_uuid":"146e3a28-04f4-4a46-b222-3e2b4103d0c7","post_uuid":"f398a272-ad43-449c-96a0-beb3180efa66","text":"Text"}');
        $user = new User(
            new UUID('146e3a28-04f4-4a46-b222-3e2b4103d0c7'),
            'username',
            'qwerty123',
            new Name('name', 'surname')
        );
        
        $postRepository = $this->postsRepository([
            new Post(
                new UUID('f398a272-ad43-449c-96a0-beb3180efa66'),
                $user,
                'title',
                'text'
            )
        ]);
        $userRepository = $this->usersRepository([
            $user
        ]);
        
        $commentRepository = $this->commentRepository();
        
        $action = new CreateComment(
            $postRepository, 
            $userRepository, 
            $this->JsonBodyUuidIdentification($userRepository),
            $commentRepository
        );
        
        $response = $action->handle($request);
        
        $this->assertInstanceOf(SuccessfulResponse::class, $response);
        
        $this->setOutputCallback(function ($data) {
            $dataDecode = json_decode(
                $data,
                associative: true,
                flags: JSON_UNESCAPED_UNICODE
            );
            
            $dataDecode['data']['uuid'] = "7ca84340-ad60-4299-b176-231a21fd9074";
            
            return json_encode(
                $dataDecode, 
                JSON_UNESCAPED_UNICODE
            );
        });
        $this->expectOutputString('{"success":true,"data":{"uuid":"7ca84340-ad60-4299-b176-231a21fd9074"}}');
        
        $response->send();
    }
    
    public function testItReturnsErrorResponseIfNotFoundUser():void 
    {
        $request = new Request([], [], '{"author_uuid":"146e3a28-04f4-4a46-b222-3e2b4108d0c7","post_uuid":"f398a272-ad43-449c-96a0-beb3180efa66","text":"text"}');
        
        $user = new User(
            new UUID('146e3a28-04f4-4a46-b222-3e2b4103d0c7'),
            'username',
            'qwerty123',
            new Name('name', 'surname')
        );
        
        $postRepository = $this->postsRepository([
            new Post(
                new UUID('f398a272-ad43-449c-96a0-beb3180efa66'),
                $user,
                'title',
                'text'
            )
        ]);
        $userRepository = $this->usersRepository([]);
        $commentRepository = $this->commentRepository();
        
        $action = new CreateComment(
            $postRepository, 
            $userRepository, 
            $this->JsonBodyUuidIdentification($userRepository), 
            $commentRepository
        );
        
        $response = $action->handle($request);
        
        $this->assertInstanceOf(ErrorResponse::class, $response);
        
        $this->expectOutputString('{"success":false,"reason":"Cannot find user: 146e3a28-04f4-4a46-b222-3e2b4108d0c7"}');
        
        $response->send();
    }
    
    public function testItReturnsErrorResponseIfNoTextProvided():void 
    {
        $request = new Request([], [], '{"author_uuid":"146e3a28-04f4-4a46-b222-3e2b4103d0c7","post_uuid":"f398a272-ad43-449c-96a0-beb3180efa66"}');
        
        $user = new User(
            new UUID('146e3a28-04f4-4a46-b222-3e2b4103d0c7'),
            'username',
            'qwerty123',
            new Name('name', 'surname')
        );
        
        $postRepository = $this->postsRepository([
            new Post(
                new UUID('f398a272-ad43-449c-96a0-beb3180efa66'),
                $user,
                'title',
                'text'
            )
        ]);
        
        $userRepository = $this->usersRepository([
            $user
        ]);
        
        $commentRepository = $this->commentRepository();
        
        $action = new CreateComment(
            $postRepository, 
            $userRepository, 
            $this->JsonBodyUuidIdentification($userRepository), 
            $commentRepository
        );
        
        $response = $action->handle($request);
        
        $this->assertInstanceOf(ErrorResponse::class, $response);
        
        $this->expectOutputString('{"success":false,"reason":"No such field: text"}');
        
        $response->send();
    }
    
    private function JsonBodyUuidIdentification($usersRepository): TokenAuthenticationInterface
    {
        return new class ($usersRepository) implements TokenAuthenticationInterface {
            
            public function __construct(
                private UserRepositoryInterface $usersRepository
            )
            { 
            }
            /**
             * @param Request $request
             * @return User
             */
            public function user(Request $request): User
            {
                $userUuid = new UUID($request->jsonBodyField('author'));
                return $this->usersRepository->get($userUuid);
            }
        };
    }
}