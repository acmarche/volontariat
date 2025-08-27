<?php

namespace AcMarche\Volontariat\Controller\Admin;

use AcMarche\Volontariat\Entity\Message;
use AcMarche\Volontariat\Form\Admin\MessageType;
use AcMarche\Volontariat\Mailer\Mailer;
use AcMarche\Volontariat\Mailer\MessageService;
use AcMarche\Volontariat\Security\RolesEnum;
use AcMarche\Volontariat\Security\TokenManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(RolesEnum::admin->value)]
class MessageController extends AbstractController
{
    public function __construct(
        private MessageService $messageService,
        private TokenManager $tokenManager,
        private Mailer $mailer,
    ) {
    }

    #[Route(path: '/admin/message/new/{query}', name: 'volontariat_admin_message_new')]
    public function new(Request $request, string $query): Response
    {
        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);

        $form->handleRequest($request);

        $destinataires = $this->messageService->getDestinataires($query);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            foreach ($destinataires as $destinataire) {
                $email = $this->messageService->getEmailEntity($destinataire);
                if ($email) {
                    $url = null;
                    if ($data->urlToken) {
                        $url = $this->tokenManager->getLinkToConnect($destinataire);
                    }

                    try {
                        $this->mailer->sendRegularMessage($email, $data, $url);
                    } catch (TransportExceptionInterface $e) {
                        $this->addFlash('danger', 'Erreur envoie pour '.$email.' '.$e->getMessage());
                    }
                }
            }

            $this->addFlash('success', 'Votre message a bien été envoyé');

            return $this->redirectToRoute('volontariat_admin_home');
        }

        return $this->render(
            '@Volontariat/admin/message/new.html.twig',
            [
                'message' => $message,
                'query' => $query,
                'destinataires' => $destinataires,
                'form' => $form,
            ]
        );
    }
}
