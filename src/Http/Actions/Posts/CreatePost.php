<?php

namespace Journal\Http\Actions\Posts;

use Journal\Blog\Exceptions\HttpException;
use Journal\Blog\Exceptions\InvalidArgumentException;
use Journal\Blog\Exceptions\UserNotFoundException;

use Journal\Blog\Post;
use Journal\Blog\UUID;

use Journal\Blog\Repositories\PostRepository\PostRepositoryInterface;
use Journal\Blog\Repositories\UserRepository\UserRepositoryInterface;

use Journal\Http\Actions\ActionInterface;

use Journal\Http\ErrorResponse;
use Journal\Http\Request;
use Journal\Http\Response;
use Journal\Http\SuccessfulResponse;

class CreatePost implements ActionInterface
{
    public function __construct(
        private PostRepositoryInterface $postsRepository,
        private UserRepositoryInterface $usersRepository,
    )
    {
    }
    public function handle(Request $request): Response
    {
        try {
            $authorUuid = new UUID($request->jsonBodyField('author'));
        } catch (HttpException | InvalidArgumentException $e) {
            return new ErrorResponse($e->getMessage());
        }
        
        try {
            $user = $this->usersRepository->get($authorUuid);
        } catch (UserNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }
        $newPostUuid = UUID::random();
        
        
        try {
            $post = new Post(
                $newPostUuid,
                $user,
                $request->jsonBodyField('title'),
                $request->jsonBodyField('text'),
            );
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }
        $this->postsRepository->save($post);

        return new SuccessfulResponse([
            'uuid' => (string) $newPostUuid,
        ]);
    }
}