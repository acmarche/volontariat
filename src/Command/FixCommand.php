<?php

namespace AcMarche\Volontariat\Command;

use AcMarche\Volontariat\Repository\AssociationRepository;
use AcMarche\Volontariat\Security\RolesEnum;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'volontariat:fix',
    description: 'Fix command',
)]
class FixCommand extends Command
{
    public function __construct(
        private readonly AssociationRepository $associationRepository,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        foreach ($this->associationRepository->findAll() as $association) {
            if ($association->valider) {
                if ($user = $association->user) {
                    if (!$user->hasRole(RolesEnum::association->value)) {
                        $user->addRole(RolesEnum::association->value);
                        $io->writeln("Role added to user {$user->email}");
                    }
                }
            } else {
                if ($user = $association->user) {
                    if ($user->hasRole(RolesEnum::association->value)) {
                        $user->removeRole(RolesEnum::association->value);
                        $io->writeln("Role removed to user {$user->email}");
                    }
                }
            }
        }

        try {
            $this->associationRepository->flush();
        } catch (Exception $exception) {
            $io->error($exception->getMessage());
        }

        return Command::SUCCESS;
    }
}
