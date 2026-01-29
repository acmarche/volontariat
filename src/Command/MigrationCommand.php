<?php

namespace AcMarche\Volontariat\Command;

use AcMarche\Volontariat\Repository\AssociationRepository;
use AcMarche\Volontariat\Repository\UserRepository;
use AcMarche\Volontariat\Repository\VolontaireRepository;
use AcMarche\Volontariat\Security\TokenManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'volontariat:migration',
    description: 'Migrate passwords from users table to association/volontaire tables, then delete non-admin users',
)]
class MigrationCommand extends Command
{
    public function __construct(
        private readonly AssociationRepository $associationRepository,
        private readonly VolontaireRepository $volontaireRepository,
        private readonly UserRepository $userRepository,
        private TokenManager $tokenManager
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Show what would be done without making changes')
            ->addOption(
                'find-duplicates',
                null,
                InputOption::VALUE_NONE,
                'Find volontaires and associations with the same email'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $dryRun = $input->getOption('dry-run');

        if ($input->getOption('find-duplicates')) {
            $this->findSameEmails($io);

            return Command::SUCCESS;
        }

        if ($dryRun) {
            $io->note('Dry-run mode: no changes will be made');
        }

        foreach ($this->userRepository->findAll() as $user) {

            if ($user->hasRole('ROLE_VOLONTARIAT_ADMIN')) {
                $io->warning("User {$user->email} is admin");
                continue;
            }

            $association = $this->associationRepository->findAssociationByUser($user);
            $volontaire = $this->volontaireRepository->findAssociationByUser($user);

            if (!$association && !$volontaire) {
                $io->warning("User {$user->email} has no matching association or volontaire");
                $this->userRepository->remove($user);
                continue;
            }
            if ($association && $volontaire) {
                $io->warning("User {$user->email} has both matching association and volontaire");
                continue;
            }
            if ($volontaire) {
                $volontaire->password = $user->password;
                $volontaire->salt = $user->salt;
                $volontaire->user = null;
                $this->userRepository->remove($user);
            }
            if ($association) {
                $association->password = $user->password;
                $association->salt = $user->salt;
                $association->user = null;
                $this->userRepository->remove($user);
            }
        }

        $this->userRepository->flush();
        $io->info("Create tokens for user");
        $this->tokenManager->createForAllUsers();
        $io->info("Same emails found:");
        $this->findSameEmails($io);

        return Command::SUCCESS;

    }

    private function findSameEmails(SymfonyStyle $io): array
    {
        $duplicates = [];

        $volontaires = $this->volontaireRepository->findAll();
        $volontairesByEmail = [];
        foreach ($volontaires as $volontaire) {
            $email = strtolower(trim($volontaire->email));
            $volontairesByEmail[$email] = $volontaire;
        }

        $associations = $this->associationRepository->findAll();
        foreach ($associations as $association) {
            if (!$association->email) {
                continue;
            }
            $email = strtolower(trim($association->email));
            if (isset($volontairesByEmail[$email])) {
                $volontaire = $volontairesByEmail[$email];
                $duplicates[] = [
                    'email' => $email,
                    'volontaire' => $volontaire,
                    'association' => $association,
                ];
                $io->warning(
                    sprintf(
                        'Duplicate email: %s - Volontaire: %s %s (ID: %d) / Association: %s (ID: %d)',
                        $email,
                        $volontaire->name,
                        $volontaire->surname,
                        $volontaire->id,
                        $association->name,
                        $association->id
                    )
                );
            }
        }

        $io->info(sprintf('Found %d duplicate emails between volontaires and associations', count($duplicates)));

        return $duplicates;
    }
}
