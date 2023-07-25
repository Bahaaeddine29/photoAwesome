<?php

namespace App\Command;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:set-admin',
    description: 'Add a short description for your command',
)]
class SetAdminCommand extends Command
{
    public function __construct(
        private UserRepository $userRepository,
        private EntityManagerInterface $entityManager
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED,)
           
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
       
        $email = $input->getArgument('email');
        $user = $this->userRepository->findOneBy(['email' => $email]);

        if ($user === null) {
            $output-> writeln( "User" . $email ."not found"); 
            return Command::FAILURE; 
        }

        $user->setRoles(['ROLE_ADMIN']); 
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $output->writeln('User' . $email . 'is now A');

        return Command::SUCCESS;
    }
}
