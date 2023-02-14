<?php

namespace Journal;

use PDO;

use Journal\Blog\Repositories\PostRepository\{ 
    SqliteCommentsRepository,
    SqlitePostsRepository
};

use Journal\Blog\Repositories\UserRepository\{ 
    InMemoryUsersRepository,
    SqliteUsersRepository,
};

class MasterRepository {
    private SqliteUsersRepository $userRepository;
    private SqlitePostsRepository $postRepository;
    private SqliteCommentsRepository $commentRepository;

    function __construct(string $DIR)
    {
        $this->userRepository = new SqliteUsersRepository(new PDO($DIR));
        $this->postRepository = new SqlitePostsRepository(new PDO($DIR));
        $this->commentRepository = new SqliteCommentsRepository(new PDO($DIR));
    }
    
    public function userRepo():SqliteUsersRepository
    {
        return $this->userRepository;
    }
    public function commentRepo():SqliteCommentsRepository
    {
        return $this->commentRepository;
    }

    public function postRepo():SqlitePostsRepository
    {
        return $this->postRepository;
    }

}