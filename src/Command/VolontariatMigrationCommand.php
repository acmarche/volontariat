<?php

namespace AcMarche\Volontariat\Command;

use AcMarche\Volontariat\Repository\AssociationRepository;
use AcMarche\Volontariat\Repository\UserRepository;
use AcMarche\Volontariat\Repository\VolontaireRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'volontariat:migration',
    description: 'Add a short description for your command',
)]
class VolontariatMigrationCommand extends Command
{
    public function __construct(
        private AssociationRepository $associationRepository,
        private VolontaireRepository $volontaireRepository,
        private UserRepository $userRepository
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        foreach ($this->associationRepository->findAll() as $association) {
            //    $association->generateSlug();
        }
        //  $this->associationRepository->flush();

        foreach ($this->userRepository->findAll() as $user) {
            $volontaires = $this->volontaireRepository->search(['user' => $user]);
            foreach ($volontaires as $volontaire) {
                $volontaire->user = $user;
            }
            $associations = $this->associationRepository->search(['user' => $user]);
            foreach ($associations as $association) {
                $association->user = $user;
            }
        }
        $this->associationRepository->flush();
        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}
