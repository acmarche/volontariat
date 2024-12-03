<?php

namespace AcMarche\Volontariat\MessageHandler;

use AcMarche\Volontariat\Mailer\Mailer;
use AcMarche\Volontariat\Message\BesoinCreated;
use AcMarche\Volontariat\Repository\BesoinRepository;
use AcMarche\Volontariat\Repository\VolontaireRepository;
use AcMarche\Volontariat\Security\TokenManager;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler()]
final class BesoinCreatedHandler
{
    public function __construct(
        private readonly VolontaireRepository $volontaireRepository,
        private readonly BesoinRepository $besoinRepository,
        private readonly TokenManager $tokenManager,
        private readonly Mailer $mailer,
    ) {}

    public function __invoke(BesoinCreated $besoinCreated): void
    {
        $besoin = $this->besoinRepository->find($besoinCreated->getBesoinId());
        if (!$besoin) {
            return;
        }
        $association = $besoin->getAssociation();
        foreach ($this->volontaireRepository->findVolontairesWantBeNotified() as $volontaire) {
            $urlLink = $this->tokenManager->getLinkToConnect($volontaire);
            try {
                $this->mailer->sendNewBesoin($besoin, $association, $volontaire, $urlLink);
            } catch (\Exception|TransportExceptionInterface $e) {
                //dd($e->getMessage());
            }
        }
    }
}