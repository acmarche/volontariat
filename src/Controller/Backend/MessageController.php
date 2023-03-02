<?php

namespace AcMarche\Volontariat\Controller\Backend;

use AcMarche\Volontariat\Entity\Message;
use AcMarche\Volontariat\Form\MessagePublicType;
use AcMarche\Volontariat\Mailer\Mailer;
use AcMarche\Volontariat\Mailer\MessageService;
use AcMarche\Volontariat\Repository\AssociationRepository;
use AcMarche\Volontariat\Repository\VolontaireRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/backend/message')]
#[IsGranted('ROLE_VOLONTARIAT')]
class MessageController extends AbstractController
{
    public function __construct(
        private AssociationRepository $associationRepository,
        private VolontaireRepository $volontaireRepository,
        private MessageService $messageService,
        private Mailer $mailer
    ) {
    }

    #[Route(path: '/new', name: 'volontariat_backend_message_new')]

    public function newAction(Request $request): Response
    {
        if (!$this->canAccess()) {
            $this->addFlash('warning', 'Vous ne pouvez pas accéder à cette page');
            $this->redirectToRoute('volontariat_dashboard');
        }
        $session = $request->getSession();
        $user = $this->getUser();
        $associations = $this->associationRepository->getAssociationsByUser($user, true);
        $froms = $this->messageService->getFroms($user, $associations);
        $message = new Message();
        // $message->setFroms($froms);
        $form = $this->createForm(
            MessagePublicType::class,
            $message,
            ['froms' => $froms]
        )
            ->add('submit', SubmitType::class, array('label' => 'Envoyer'));
        $form->handleRequest($request);
        $destinataires = [];
        $key = VolontariatConstante::VOLONTAIRE_SEARCH;
        if ($session->has($key)) {
            $data = unserialize($session->get($key), false);
            $destinataires = $this->volontaireRepository->search($data);
        }
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $from = $data->getFroms();

            $this->mailer->sendMessage($data, $destinataires, $from);

            $this->addFlash(
                'success',
                'Votre message a bien été envoyé à '.(is_countable($destinataires) ? count(
                    $destinataires
                ) : 0).' destinataires : '.
                implode(
                    ', ',
                    $destinataires
                )
            );

            return $this->redirectToRoute('volontariat_volontaire');
        }

        return $this->render(
            '@Volontariat/message/new.html.twig',
            [
                'message' => $message,
                'destinataires' => $destinataires,
                'form' => $form->createView(),
            ]
        );
    }
}
