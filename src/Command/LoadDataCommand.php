<?php

namespace AcMarche\Volontariat\Command;

use AcMarche\Volontariat\Manager\TokenManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class LoadDataCommand extends Command
{
    protected static $defaultName = 'acvolontariat:generatetoken';

    public function __construct(private TokenManager $tokenManager, string $name = null)
    {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this->setDescription('Génère des tokens');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->tokenManager->createForAllUsers();
        return 0;
    }


}
