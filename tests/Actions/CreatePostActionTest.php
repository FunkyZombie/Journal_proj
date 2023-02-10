<?php

namespace Journal\Blog\UnitTests\Actions;

use Journal\Blog\Exceptions\PostNotFoundException;
use Journal\Blog\Exceptions\UserNotFoundException;
use Journal\Blog\Name;
use Journal\Blog\Post;
use Journal\Blog\Repositories\PostRepository\PostRepositoryInterface;
use Journal\Blog\Repositories\UserRepository\UserRepositoryInterface;
use Journal\Blog\User;
use Journal\Blog\UUID;
use Journal\Http\Actions\Posts\CreatePost;
use Journal\Http\ErrorResponse;
use Journal\Http\Request;

use Journal\Http\SuccessfulResponse;
use PHPUnit\Framework\TestCase;

class CreatePostActionTest extends TestCase
{
    private function postsRepository(): PostRepositoryInterface
    {
        return new class() implements PostRepositoryInterface 
        {
            private bool $called = false;
            
            public function save(Post $post):void
            {
                $this->called = true;
            }

            public function get(UUID $uuid): Post
            {
                throw new PostNotFoundException('Not found');
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
    
    public function testItReturnsSuccessfulResponse(): void
    {
        $request = new Request([], [], '{"author":"146e3a28-04f4-4a46-b222-3e2b4103d0c7","title":"Title","text":"Text"}');
        $postRepository = $this->postsRepository();
        $userRepository = $this->usersRepository([
            new User(
                new UUID('146e3a28-04f4-4a46-b222-3e2b4103d0c7'),
                'username',
                new Name('name', 'surname')
            )
        ]);
        $action = new CreatePost($postRepository, $userRepository);
        $response = $action->handle($request);
        
        $this->assertInstanceOf(SuccessfulResponse::class, $response);
        
        $this->setOutputCallback(function ($data) {
            $dataDecode = json_decode(
                $data,
                associative: true,
                flags: JSON_UNESCAPED_UNICODE
            );
            
            $dataDecode['data']['uuid'] = "f398a272-ad43-449c-96a0-beb3180efa66";
            
            return json_encode(
                $dataDecode, 
                JSON_UNESCAPED_UNICODE
            );
        });
        $this->expectOutputString('{"success":true,"data":{"uuid":"f398a272-ad43-449c-96a0-beb3180efa66"}}');
        
        $response->send();
    }
    
    public function testItReturnsErrorResponseIfNotFoundUser():void 
    {
        $request = new Request([], [], '{"author":"146e3a28-04f4-4a46-b222-3e2b4108d0c7","title":"title","text":"text"}');
        
        $postRepository = $this->postsRepository();
        $userRepository = $this->usersRepository([]);
        
        $action = new CreatePost($postRepository, $userRepository);
        
        $response = $action->handle($request);
        
        $this->assertInstanceOf(ErrorResponse::class, $response);
        
        $this->expectOutputString('{"success":false,"reason":"Cannot find user: 146e3a28-04f4-4a46-b222-3e2b4108d0c7"}');
        
        $response->send();
    }
    
    public function testItReturnsErrorResponseIfNoTextProvided():void 
    {
        $request = new Request([], [], '{"author":"146e3a28-04f4-4a46-b222-3e2b4103d0c7","title":"title"}');
        
        $postRepository = $this->postsRepository();
        $userRepository = $this->usersRepository([
            new User(
                new UUID('146e3a28-04f4-4a46-b222-3e2b4103d0c7'),
                'username',
                new Name('name', 'surname')
            )
        ]);
        
        $action = new CreatePost($postRepository, $userRepository);
        
        $response = $action->handle($request);
        
        $this->assertInstanceOf(ErrorResponse::class, $response);
        
        $this->expectOutputString('{"success":false,"reason":"No such field: text"}');
        
        $response->send();
    }
}