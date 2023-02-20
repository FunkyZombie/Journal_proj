<?php

namespace Journal\Http\Actions\Posts;

use Journal\Blog\Exceptions\PostNotFoundException;

use Journal\Blog\Exceptions\UserNotFoundException;
use Journal\Blog\Repositories\PostRepository\PostRepositoryInterface;

use Journal\Blog\UUID;
use Journal\Http\Actions\ActionInterface;

use Journal\Http\Auth\AuthException;
use Journal\Http\Auth\TokenAuthenticationInterface;
use Journal\Http\ErrorResponse;
use Journal\Http\Request;
use Journal\Http\Response;
use Journal\Http\SuccessfulResponse;
use Psr\Log\LoggerInterface;

class DeletePost implements ActionInterface
{
    public function __construct(
        private PostRepositoryInterface $postsRepository,
        private TokenAuthenticationInterface $authentication,
        private LoggerInterface $logger
    )
    {
    }
    public function handle(Request $request): Response
    {
        try {
            $user = $this->authentication->user($request);
        } catch (UserNotFoundException|AuthException $e) {
            return new ErrorResponse($e->getMessage());
        }
        
        $postUuid = new UUID($request->query('uuid'));
        
        try {
            $post = $this->postsRepository->get($postUuid);
        } catch (PostNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }
        
        if ($user->uuid() !== $post->author()->uuid()) {
            return new SuccessfulResponse([
                'Forbidden' => 'Not enough rights',
            ]);
        }
        
        $this->postsRepository->delete($postUuid);
        $this->logger->info("Post deleted: $postUuid");
        
        return new SuccessfulResponse([
            'uuid' => (string) $postUuid,
        ]);
    }
}