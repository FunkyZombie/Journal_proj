<?php 

require_once __DIR__ . '/vendor/autoload.php';

use Journal\Blog\Commands\{
    CreateUserCommand, 
    Arguments
};

use Journal\Blog\Exceptions\{
    UserNotFoundException, 
    AppException
};

use Journal\Blog\{
    User, 
    Name, 
    Post, 
    UUID,
    Comment
};

use Journal\Blog\Repositories\PostRepository\SqliteCommentsRepository;
use Journal\Blog\Repositories\PostRepository\SqlitePostsRepository;
use Journal\Blog\Repositories\UserRepository\SqliteUsersRepository;

$DIR = 'sqlite:' . __DIR__ . '/blog.sqlite';

$userRepository = new SqliteUsersRepository(new PDO($DIR));
$postRepository = new SqlitePostsRepository(new PDO($DIR), $userRepository);
$commentRepository = new SqliteCommentsRepository(new PDO($DIR));

$command = new CreateUserCommand($userRepository);

// $user = $master->userRepo()->save(
//     new User(UUID::random(),
//         'FunkyMonk',
//         new Name('Anatoliy', 'Shilin')
//     )
// );

$user = $userRepository->get(new UUID('146e3a28-04f4-4a46-b222-3e2b4103d0c7'));

// $post = new Post(UUID::random(), $user, 'Заголовок статьи', 'Текст статьи');

// $master->postRepo()->save($post);

$getPost = $postRepository->get(new UUID('f0adf792-11c8-4d1d-a240-c64476826a16'));

echo '<pre>';
print_r($getPost);
echo '</pre>';

// try {
//     $command->handler(Arguments::fromArgv($argv));
// } catch (AppException $e) {
//     print $e->getMessage();
// }