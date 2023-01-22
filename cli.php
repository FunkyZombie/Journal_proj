<?php 

require_once 'vendor/autoload.php';

use Journal\Articles\Comment as Comment;
use Journal\Articles\Article as Article;
use Journal\Users\User as User;

$faker = Faker\Factory::create();

if ($argv[1] === 'user') {
    $user = new User($faker->ean13(), $faker->firstName(), $faker->lastName());
    echo $user->__toString() . "\n";
}

if ($argv[1] === 'post') {
    $article = new Article($faker->text(30), $faker->text(200));
    echo $article->__toString() . "\n";
}

if ($argv[1] === 'comment') {
    $comment = new Comment($faker->text(200));
    echo $comment->__toString() . "\n";
}
