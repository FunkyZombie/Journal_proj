<?php

namespace Journal\Http\Actions\Posts;

use Journal\Blog\Exceptions\HttpException;
use Journal\Blog\Exceptions\UserNotFoundException;
use Journal\Blog\Post;
use Journal\Blog\UUID;

use Journal\Blog\Repositories\PostRepository\PostRepositoryInterface;
use Journal\Blog\Repositories\UserRepository\UserRepositoryInterface;
use Journal\Http\Actions\ActionInterface;
use Journal\Http\Auth\IdentificationInterface;
use Journal\Http\ErrorResponse;
use Journal\Http\Request;
use Journal\Http\Response;
use Journal\Http\SuccessfulResponse;
use Psr\Log\LoggerInterface;

class CreatePost implements ActionInterface
{
    public function __construct(
        private PostRepositoryInterface $postsRepository,
        private UserRepositoryInterface $usersRepository,
        private IdentificationInterface $identification,
        private LoggerInterface $logger,
    )
    {
    }
    public function handle(Request $request): Response
    {
        try {
            $author = $this->identification->user($request);
        } catch (UserNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $newPostUuid = UUID::random();
            $post = new Post(
                uuid: $newPostUuid,
                author: $author,
                title: $request->jsonBodyField('title'),
                text: $request->jsonBodyField('text'),
            );
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }
        $this->postsRepository->save($post);

        $this->logger->info("Post created: $newPostUuid");

        return new SuccessfulResponse([
            'uuid' => (string) $newPostUuid,
        ]);
    }
}