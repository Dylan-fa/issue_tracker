<?php

namespace App\Command;

use App\Repository\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:promote-admin',
    description: 'Promotes a user to admin',
)]
class PromoteAdminCommand extends Command
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('username', InputArgument::REQUIRED, 'Username of the user to promote')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $username = $input->getArgument('username');

        $user = $this->userRepository->findOneByUsername($username);

        $roles = $user->getRoles();

        if (in_array('ROLE_ADMIN', $roles)) {
            $io->error("$username is already an administrator");
            return Command::FAILURE;
        }

        $roles[] = 'ROLE_ADMIN';

        $user->setRoles($roles);

        $this->userRepository->save($user, true);

        $io->success("$username promoted to admin");

        return Command::SUCCESS;
    }
}
