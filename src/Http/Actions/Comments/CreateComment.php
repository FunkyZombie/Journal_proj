<?php

namespace Journal\Http\Actions\Comments;

use Journal\Blog\Exceptions\HttpException;
use Journal\Blog\Exceptions\InvalidArgumentException;
use Journal\Blog\Exceptions\PostNotFoundException;
use Journal\Blog\Exceptions\UserNotFoundException;

use Journal\Blog\Repositories\PostRepository\CommentRepositoryInterface;
use Journal\Blog\UUID;
use Journal\Blog\Comment;

use Journal\Blog\Repositories\PostRepository\PostRepositoryInterface;
use Journal\Blog\Repositories\UserRepository\UserRepositoryInterface;

use Journal\Http\Actions\ActionInterface;

use Journal\Http\Auth\TokenAuthenticationInterface;
use Journal\Http\ErrorResponse;
use Journal\Http\Request;
use Journal\Http\Response;
use Journal\Http\SuccessfulResponse;

class CreateComment implements ActionInterface
{
    public function __construct(
        private PostRepositoryInterface $postsRepository,
        private UserRepositoryInterface $usersRepository,
        private TokenAuthenticationInterface $authentication,
        private CommentRepositoryInterface $commentsRepository
    )
    {
    }
    public function handle(Request $request): Response
    {
        try {
            $authorUuid = new UUID($request->jsonBodyField('author_uuid'));
            $postUuid = new UUID($request->jsonBodyField('post_uuid'));
        } catch (HttpException | InvalidArgumentException $e) {
            return new ErrorResponse($e->getMessage());
        }
        
        try {
            $this->postsRepository->get($postUuid);
        } catch (PostNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }
        
        try {
            $this->usersRepository->get($authorUuid);
        } catch (UserNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }
        
        $newCommentUuid = UUID::random();
        
        try {
            $comment = new Comment(
                $newCommentUuid,
                $authorUuid,
                $postUuid,
                $request->jsonBodyField('text'),
            );
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }
        $this->commentsRepository->save($comment);

        return new SuccessfulResponse([
            'uuid' => (string) $newCommentUuid,
        ]);
    }
}