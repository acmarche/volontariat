<?php

namespace AcMarche\Volontariat\Command;

use AcMarche\Volontariat\Mailer\Mailer;
use AcMarche\Volontariat\Mailer\MessageService;
use AcMarche\Volontariat\Security\TokenManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

#[AsCommand(
    name: 'volontariat:mailer',
    description: 'Add a short description for your command',
)]
class MailerCommand extends Command
{
    public function __construct(
        private MessageService $messageService,
        private TokenManager $tokenManager,
        private RouterInterface $router,
        private Mailer $mailer
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('query', InputArgument::REQUIRED, 'Argument description');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $query = $input->getArgument('query');

        if ($query) {
            $destinataires = $this->messageService->getDestinataires($query);
            $urlAssociations = $this->router->generate(
                'volontariat_association',
                [],
                UrlGeneratorInterface::ABSOLUTE_URL
            );
            $urlVolontaires = $this->router->generate(
                'volontariat_volontaire',
                [],
                UrlGeneratorInterface::ABSOLUTE_URL
            );

            foreach ($destinataires as $association) {
                $email = $this->messageService->getEmailEntity($association);
                if ($email) {
                    $urlAccount = $this->tokenManager->getLinkToConnect($association);
                    try {
                        $this->mailer->sendAutoAssociation($association,$email, $urlAccount, $urlAssociations, $urlVolontaires);
                    } catch (TransportExceptionInterface $e) {
                        $io->error($e->getMessage());
                    }
                }
                break;
            }
            $io->success('Sent to '.count($destinataires));

            return Command::SUCCESS;
        }

        return Command::FAILURE;
    }
}
