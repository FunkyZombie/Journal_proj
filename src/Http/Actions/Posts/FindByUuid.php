<?php

namespace Journal\Http\Actions\Posts;

use Journal\Blog\Exceptions\HttpException;
use Journal\Blog\Exceptions\PostNotFoundException;

use Journal\Blog\Repositories\PostRepository\PostRepositoryInterface;

use Journal\Blog\UUID;
use Journal\Http\Actions\ActionInterface;
use Journal\Http\ErrorResponse;
use Journal\Http\Request;
use Journal\Http\Response;
use Journal\Http\SuccessfulResponse;
use Psr\Log\LoggerInterface;

class FindByUuid implements ActionInterface
{
    public function __construct(
        private PostRepositoryInterface $postRepository,
        private LoggerInterface $logger
    )
    {}
    public function handle(Request $request): Response
    {
        try {
            $postUuid = $request->query('uuid');
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }
        
        try {
            $post = $this->postRepository->get(new UUID($postUuid));
        } catch (PostNotFoundException $e) {
            $this->logger->warning($e->getMessage() . ': '. $postUuid);
            return new ErrorResponse($e->getMessage());
        }
        
        return new SuccessfulResponse([
            'title' => $post->title(),
            'text' => $post->text(),
        ]);
    }
}