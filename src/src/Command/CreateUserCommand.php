<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-user',
    description: 'Creates a new user with API key',
)]
class CreateUserCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'The email of the user')
            ->addArgument('password', InputArgument::REQUIRED, 'The password of the user')
            ->addArgument('roles', InputArgument::OPTIONAL, 'The roles of the user (comma separated)')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $email = $input->getArgument('email');
            $password = $input->getArgument('password');
            $roles = $input->getArgument('roles') ? explode(',', $input->getArgument('roles')) : ['ROLE_USER'];

            $user = new User();
            $user->setEmail($email);
            $user->setRoles($roles);

            $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
            $user->setPassword($hashedPassword);

            $apiKey = bin2hex(random_bytes(32));
            $user->setApiKey($apiKey);

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $io->success([
                'User created successfully',
                'Email: '.$email,
                'API Key: '.$apiKey,
            ]);

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $io->error(sprintf('User creation failed with error: %s', $e->getMessage()));
        }

        return Command::FAILURE;
    }
}
