<?php
namespace Journal\Blog\Commands\FakeData;

use Journal\Blog\Comment;
use Journal\Blog\Name;
use Journal\Blog\Post;
use Journal\Blog\Repositories\PostRepository\CommentRepositoryInterface;
use Journal\Blog\Repositories\PostRepository\PostRepositoryInterface;
use Journal\Blog\Repositories\UserRepository\UserRepositoryInterface;
use Journal\Blog\User;
use Journal\Blog\UUID;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PopulateDB extends Command
{
    // Внедряем генератор тестовых данных и
// репозитории пользователей и статей
    public function __construct(
        private \Faker\Generator $faker,
        private UserRepositoryInterface $usersRepository,
        private PostRepositoryInterface $postsRepository,
        private CommentRepositoryInterface $commentRepository,
    )
    {
        parent::__construct();
    }
    protected function configure(): void
    {
        $this
            ->setName('fake-data:populate-db')
            ->setDescription('Populates DB with fake data')
            ->addOption(
                'users-number',
                'u',
                InputOption::VALUE_OPTIONAL,
                'Users number',
            )
            ->addOption(
                'posts-number',
                'p',
                InputOption::VALUE_OPTIONAL,
                'Posts number',
            )
            ->addOption(
                'comments-number',
                'c',
                InputOption::VALUE_OPTIONAL,
                'Comments number',
            );
    }
    protected function execute(
        InputInterface $input,
        OutputInterface $output,
    ): int
    {
        $usersCount = $input->getOption('users-number') ?? 10;
        $postsCount = $input->getOption('posts-number') ?? 15;
        $commentsCount = $input->getOption('comments-number') ?? 7;
        
        $users = [];
        $posts = [];
        
        for ($i = 0; $i < $usersCount; $i++) {
            $user = $this->createFakeUser();
            $users[] = $user;
            $output->writeln('User created: ' . $user->username());
        }

        foreach ($users as $user) {
            for ($i = 0; $i < $postsCount; $i++) {
                $post = $this->createFakePost($user);
                $posts[] = $post;
                $output->writeln('Post created: ' . $post->title());
            }
        }
        
        foreach ($posts as $post) {
            for ($i = 0; $i < $commentsCount; $i++) {
                $user = $users[rand(0, (count($users) - 1))];
                $this->createFakerComment($user, $post);
                $output->writeln('Comment created by' . $user->username());
            }
        }
        
        return Command::SUCCESS;
    }
    private function createFakeUser(): User
    {
        $user = User::createFrom(
            $this->faker->userName,
            $this->faker->password,
            new Name(
                    $this->faker->firstName,
                    $this->faker->lastName
            )
        );
            
        $this->usersRepository->save($user);
        
        return $user;
    }
    private function createFakePost(User $author): Post
    {
        $post = new Post(
            UUID::random(),
            $author,
            $this->faker->sentence(6, true),
            $this->faker->realText
        );
        
        $this->postsRepository->save($post);
        return $post;
    }
    private function createFakerComment(User $author, Post $post): Comment
    {
        $comment = new Comment(
            UUID::random(),
            $author->uuid(),
            $post->uuid(),
            $this->faker->realText
        );
            
        $this->commentRepository->save($comment);
        
        return $comment;
    }
}