<?php
namespace Journal\Blog\Commands\Users;

use Journal\Blog\Name;
use Journal\Blog\Repositories\UserRepository\UserRepositoryInterface;
use Journal\Blog\User;
use Journal\Blog\UUID;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateUser extends Command
{
    public function __construct(
        private UserRepositoryInterface $usersRepository,
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('users:update')
            ->setDescription('Updates a user')
            ->addArgument(
                'uuid',
                InputArgument::REQUIRED,
                'UUID of a user to update'
            )
            ->addOption(
                'first-name',
                'f',
                InputOption::VALUE_OPTIONAL,
                'First name',
            )
            ->addOption(
                'last-name',
                'l',
                InputOption::VALUE_OPTIONAL,
                'Last name',
            );
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output,
    ): int
    {
        $firstName = $input->getOption('first-name');
        
        $lastName = $input->getOption('last-name');
        
        if (empty($firstName) && empty($lastName)) {
            $output->writeln('Nothing to update');
            return Command::SUCCESS;
        }
        
        $uuid = new UUID($input->getArgument('uuid'));
        
        $user = $this->usersRepository->get($uuid);
        
        $updatedName = new Name(
            first_name: empty($firstName)
            ? $user->name()->firstName() : $firstName,
            last_name: empty($lastName)
            ? $user->name()->lastName() : $lastName,
        );
        
        $updatedUser = new User(
            uuid: $uuid,
            username: $user->username(),
            hashedPassword: $user->hashedPassword(),
            name: $updatedName
        );
        
        $this->usersRepository->save($updatedUser);
        $output->writeln("User updated: $uuid");
        return Command::SUCCESS;
    }
}