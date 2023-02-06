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

use Journal\MasterRepository as MasterRepository;

$master = new MasterRepository('sqlite:' . __DIR__ . '/blog.sqlite');

$command = new CreateUserCommand($user = $master->userRepo());

$user = $master->userRepo()->get(new UUID('5b1da8ae-9a21-45c2-9dcc-52189a966979'));
// $post = new Post(UUID::random(), $user->uuid(), 'Заголовок статьи', 'Текст статьи');
$post = $master->postRepo()->get(new UUID('8e368fdb-c8b8-46f7-bb30-f2e59d3e1ff1'));

$comment = new Comment(UUID::random(), $user->uuid(), $post->uuid(), 'Еще один рандомный комментарий');

$commentOnPost = $master->commentRepo()->getAllCommentsOnPost(new UUID('8e368fdb-c8b8-46f7-bb30-f2e59d3e1ff1'));

echo "<pre>";
var_dump($post);
echo "</pre>";

// try {
//     $command->handler(Arguments::fromArgv($argv));
// } catch (AppException $e) {
//     print $e->getMessage();
// }