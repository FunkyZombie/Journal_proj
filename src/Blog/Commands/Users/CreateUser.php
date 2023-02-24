<?php

namespace Journal\Blog\Commands\Users;

use Journal\Blog\Exceptions\UserNotFoundException;
use Journal\Blog\Name;
use Journal\Blog\Repositories\UserRepository\UserRepositoryInterface;
use Journal\Blog\User;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateUser extends Command
{
    public function __construct(
        private UserRepositoryInterface $usersRepository
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('users:create')
            ->setDescription('Create new user')
            ->addArgument('username', InputArgument::REQUIRED, 'Username')
            ->addArgument('password', InputArgument::REQUIRED, 'Password')
            ->addArgument('first_name', InputArgument::REQUIRED, 'First name')
            ->addArgument('last_name', InputArgument::REQUIRED, 'Last name');
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output,
    ): int
    {
        $output->writeln('Create user command started');
        $username = $input->getArgument('username');
        if ($this->userExists($username)) {
            // Используем OutputInterface вместо логгера
            $output->writeln("User already exists: $username");
            return Command::FAILURE;
        }

        $user = User::createFrom(
            $username,
            $input->getArgument('password'),
            new Name(
                $input->getArgument('first_name'),
                $input->getArgument('last_name')
            )
        );

        $this->usersRepository->save($user);
        $output->writeln('User created: ' . $user->uuid());

        return Command::SUCCESS;
    }

    private function userExists(string $username): bool
    {
        try {
            $this->usersRepository->getByUsername($username);
        } catch (UserNotFoundException) {
            return false;
        }
        return true;
    }
}