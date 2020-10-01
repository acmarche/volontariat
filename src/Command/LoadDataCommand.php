<?php

namespace AcMarche\Volontariat\Command;

use AcMarche\Volontariat\Manager\TokenManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class LoadDataCommand extends Command
{
    /**
     * @var TokenManager
     */
    private $tokenManager;

    public function __construct(string $name = null, TokenManager $tokenManager)
    {
        parent::__construct($name);
        $this->tokenManager = $tokenManager;
    }

    protected function configure()
    {
        $this
            ->setName('acvolontariat:generatetoken')
            ->setDescription('Génère des tokens');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->tokenManager->createForAllUsers();
    }


}
