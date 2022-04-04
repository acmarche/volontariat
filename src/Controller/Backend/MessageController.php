<?php

namespace AcMarche\Volontariat\Controller\Backend;

use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpFoundation\Response;
use AcMarche\Volontariat\Entity\Message;
use AcMarche\Volontariat\Form\MessagePublicType;
use AcMarche\Volontariat\Repository\VolontaireRepository;
use AcMarche\Volontariat\Service\AssociationService;
use AcMarche\Volontariat\Service\Mailer;
use AcMarche\Volontariat\Service\MessageService;
use AcMarche\Volontariat\Service\VolontariatConstante;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Besoin controller.
 *
 *
 */
#[Route(path: '/backend/message')]
#[IsGranted('ROLE_VOLONTARIAT')]
class MessageController extends AbstractController
{
    public function __construct(private AssociationService $associationService, private VolontaireRepository $volontaireRepository, private MessageService $messageService, private Mailer $mailer)
    {
    }
    /**
     * Displays a form to create a new message.
     *
     *
     * @throws NonUniqueResultException
     */
    #[Route(path: '/new', name: 'volontariat_backend_message_new')]
    public function newAction(Request $request) : Response
    {
        if (!$this->canAccess()) {
            $this->addFlash('warning', 'Vous ne pouvez pas accéder à cette page');
            $this->redirectToRoute('volontariat_dashboard');
        }
        $session = $request->getSession();
        $user = $this->getUser();
        $associations = $this->associationService->getAssociationsByUser($user, true);
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
                'Votre message a bien été envoyé à '.(is_countable($destinataires) ? count($destinataires) : 0).' destinataires : '.
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
    protected function canAccess(): bool
    {
        $user = $this->getUser();
        if ($user->hasRole('ROLE_VOLONTARIAT_ADMIN')) {
            return true;
        }

        return $this->associationService->hasValidAssociation($user);
    }
}
