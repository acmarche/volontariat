<?php

namespace AcMarche\Volontariat\Command;

use Exception;
use AcMarche\Volontariat\Repository\AssociationRepository;
use AcMarche\Volontariat\Repository\BesoinRepository;
use AcMarche\Volontariat\Repository\VolontaireRepository;
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
        private readonly VolontaireRepository $volontaireRepository,
        private readonly BesoinRepository $besoinRepository,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $symfonyStyle = new SymfonyStyle($input, $output);
        foreach ($this->associationRepository->findAll() as $volontaire) {
           $volontaire->uuid = $volontaire->generateUuid();
        }

        foreach ($this->besoinRepository->findAll() as $volontaire) {
           $volontaire->uuid = $volontaire->generateUuid();
        }

        foreach ($this->volontaireRepository->findAll() as $volontaire) {
           $volontaire->uuid = $volontaire->generateUuid();
        }

        try {
            $this->associationRepository->flush();
        } catch (Exception $exception) {
            $symfonyStyle->error($exception->getMessage());
        }

        return Command::SUCCESS;
    }
}