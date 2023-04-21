<?php

namespace AcMarche\Volontariat\Controller\Admin;

use AcMarche\Volontariat\Entity\Message;
use AcMarche\Volontariat\Form\Admin\MessageType;
use AcMarche\Volontariat\Mailer\Mailer;
use AcMarche\Volontariat\Mailer\MessageService;
use AcMarche\Volontariat\Repository\AssociationRepository;
use AcMarche\Volontariat\Repository\MessageRepository;
use AcMarche\Volontariat\Repository\VolontaireRepository;
use AcMarche\Volontariat\Security\TokenManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/admin/message')]
#[IsGranted('ROLE_VOLONTARIAT_ADMIN')]
class MessageController extends AbstractController
{
    public function __construct(
        private MessageService $messageService,
        private AssociationRepository $associationRepository,
        private VolontaireRepository $volontaireRepository,
        private MessageRepository $messageRepository,
        private TokenManager $tokenManager,
        private Mailer $mailer,
    ) {
    }

    #[Route(path: '/new/{query}', name: 'volontariat_admin_message_new')]
    public function new(Request $request, string $query): Response
    {
        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);

        $form->handleRequest($request);
        $destinataires = $this->messageService->getDestinataires($query);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            foreach ($destinataires as $association) {
                $email = $this->messageService->getEmailEntity($association);
                if ($email) {
                    $url = null;
                    if ($data->urlToken) {
                        $url = $this->tokenManager->getLinkToConnect($association);
                    }
                    try {
                        $this->mailer->sendRegularMessage($email, $data, $url);
                    } catch (TransportExceptionInterface $e) {
                        $this->addFlash('danger', 'Erreur envoie pour '.$email.' '.$e->getMessage());
                    }
                }
            }

            $this->addFlash('success', 'Votre message a bien été envoyé');

            return $this->redirectToRoute('volontariat_admin_message_new');
        }

        return $this->render(
            '@Volontariat/admin/message/new.html.twig',
            [
                'message' => $message,
                'query' => $query,
                'destinataires' => $destinataires,
                'form' => $form->createView(),
            ]
        );
    }
}
