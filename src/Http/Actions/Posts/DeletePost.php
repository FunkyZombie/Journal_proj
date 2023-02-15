<?php

namespace Journal\Http\Actions\Posts;

use Journal\Blog\Exceptions\PostNotFoundException;

use Journal\Blog\Repositories\PostRepository\PostRepositoryInterface;

use Journal\Blog\UUID;
use Journal\Http\Actions\ActionInterface;

use Journal\Http\ErrorResponse;
use Journal\Http\Request;
use Journal\Http\Response;
use Journal\Http\SuccessfulResponse;
use Psr\Log\LoggerInterface;

class DeletePost implements ActionInterface
{
    public function __construct(
        private PostRepositoryInterface $postsRepository,
        private LoggerInterface $logger
    )
    {
    }
    public function handle(Request $request): Response
    {
        $postUuid = new UUID($request->query('uuid'));
        try {
            $this->postsRepository->get($postUuid);
        } catch (PostNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }
        
        $this->postsRepository->delete($postUuid);
       
        $this->logger->info("Post deleted: $postUuid");
        
        return new SuccessfulResponse([
            'uuid' => (string) $postUuid,
        ]);
    }
}