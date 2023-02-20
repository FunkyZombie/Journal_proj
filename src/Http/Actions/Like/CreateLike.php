<?php

namespace Journal\Http\Actions\Like;

use Journal\Blog\Exceptions\HttpException;
use Journal\Blog\Exceptions\InvalidArgumentException;
use Journal\Blog\Exceptions\PostNotFoundException;
use Journal\Blog\Exceptions\UserNotFoundException;

use Journal\Blog\Like;
use Journal\Blog\UUID;

use Journal\Blog\Repositories\LikeRepository\LikeRepositoryInterface;
use Journal\Blog\Repositories\PostRepository\PostRepositoryInterface;
use Journal\Blog\Repositories\UserRepository\UserRepositoryInterface;

use Journal\Http\Actions\ActionInterface;

use Journal\Http\Auth\AuthException;
use Journal\Http\Auth\TokenAuthenticationInterface;
use Journal\Http\ErrorResponse;
use Journal\Http\Request;
use Journal\Http\Response;
use Journal\Http\SuccessfulResponse;
use Psr\Log\LoggerInterface;

class CreateLike implements ActionInterface
{
    public function __construct(
        private PostRepositoryInterface $postsRepository,
        private UserRepositoryInterface $usersRepository,
        private LikeRepositoryInterface $likeRepository,
        private TokenAuthenticationInterface $authentication,
        private LoggerInterface $logger
    )
    {
    }
    public function handle(Request $request): Response
    {
        try {
            $user = $this->authentication->user($request);
            $postUuid = new UUID($request->jsonBodyField('post_uuid'));
        } catch (HttpException | InvalidArgumentException $e) {
            $this->logger->warning($e->getMessage());
            return new ErrorResponse($e->getMessage());
        }
        
        try {
            $this->postsRepository->get($postUuid);
        } catch (PostNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }
        
        $userUuid = new UUID($user->uuid());
        
        try {
            $this->usersRepository->get($userUuid);
        } catch (UserNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }

        if ($this->likeRepository->has($postUuid, $userUuid)) {
            $newLikeUuid = UUID::random();

            $like = new Like(
                uuid: $newLikeUuid,
                post_uuid: $postUuid,
                user_uuid: $userUuid,
            );
            $this->likeRepository->save($like);
            return new SuccessfulResponse([
                'uuid' => (string) $newLikeUuid,
            ]);
        }
        return new SuccessfulResponse([
            'forbidden' => 'You have already rated the post',
        ]);
    }
}