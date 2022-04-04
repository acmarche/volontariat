<?php

namespace AcMarche\Volontariat\Controller\Admin;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\NonUniqueResultException;
use AcMarche\Volontariat\Entity\Message;
use AcMarche\Volontariat\Form\Admin\MessageType;
use AcMarche\Volontariat\Service\Mailer;
use AcMarche\Volontariat\Service\MessageService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Besoin controller.
 *
 *
 */
#[Route(path: '/admin/message')]
#[IsGranted('ROLE_VOLONTARIAT_ADMIN')]
class MessageController extends AbstractController
{
    public function __construct(private MessageService $messageService, private Mailer $mailer, private ManagerRegistry $managerRegistry)
    {
    }
    #[Route(path: '/', name: 'volontariat_admin_message')]
    public function indexAction() : Response
    {
        $em = $this->managerRegistry->getManager();
        $messages = [];
        $args = array('is_publish' => 1);
        $user = $this->getUser();
        if ($user !== null) {
            if (true === $user->hasRole('ROLE_ADMIN')) {
                $messages = $em->getRepository(Message::class)->findAll();
            }
        } else {
            $messages = $em->getRepository(Message::class)->findBy($args);
        }
        $count = is_countable($messages) ? count($messages) : 0;
        return $this->render('@Volontariat/admin/message/index.html.twig', array('messages' => $messages, 'count' => $count));
    }
    /**
     * Displays a form to create a new Page message.
     *
     *
     * @throws NonUniqueResultException
     */
    #[Route(path: '/new/{query}', name: 'volontariat_admin_message_new')]
    public function newAction(Request $request, $query = null) : Response
    {
        $message = new Message();
        $form = $this->createForm(
            MessageType::class,
            $message,
            [
                'query' => $query,
            ]
        )
            ->add('submit', SubmitType::class, array('label' => 'Envoyer'));
        $form->handleRequest($request);
        $destinataires = $this->messageService->getDestinataires($query);
        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();

            if (!$query) {
                $query = $data->getSelectionDestinataires();
                $destinataires = $this->messageService->getDestinataires($query, true);
            }

            $this->mailer->sendMessage($data, $destinataires, null, $data->getFile());

            $this->addFlash(
                'success',
                'Votre message a bien été envoyé à '.(is_countable($destinataires) ? count($destinataires) : 0).' destinataires : '.
                implode(
                    ', ',
                    $destinataires
                )
            );

            return $this->redirectToRoute('volontariat_admin_message_new');
        }
        return $this->render(
            '@Volontariat/admin/message/new.html.twig',
            array(
                'message' => $message,
                'query' => $query,
                'destinataires' => $destinataires,
                'form' => $form->createView(),
            )
        );
    }
}
