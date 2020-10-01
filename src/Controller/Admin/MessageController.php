<?php

namespace AcMarche\Volontariat\Controller\Admin;

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
 * @Route("/admin/message")
 * @IsGranted("ROLE_VOLONTARIAT_ADMIN")
 *
 */
class MessageController extends AbstractController
{
    /**
     * @var MessageService
     */
    private $messageService;
    /**
     * @var Mailer
     */
    private $mailer;

    public function __construct(MessageService $messageService, Mailer $mailer)
    {
        $this->messageService = $messageService;
        $this->mailer = $mailer;
    }

    /**
     * @Route("/", name="volontariat_admin_message")
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $messages = [];

        $args = array('is_publish' => 1);
        $user = $this->getUser();

        if ($user) {
            if (true === $user->hasRole('ROLE_ADMIN')) {
                $messages = $em->getRepository(Message::class)->findAll();
            }
        } else {
            $messages = $em->getRepository(Message::class)->findBy($args);
        }

        $count = count($messages);

        return $this->render('admin/message/index.html.twig', array('messages' => $messages, 'count' => $count));
    }

    /**
     * Displays a form to create a new Page message.
     *
     * @Route("/new/{query}", name="volontariat_admin_message_new")
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function newAction(Request $request, $query = null)
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
                'Votre message a bien été envoyé à '.count($destinataires).' destinataires : '.
                join(
                    ', ',
                    $destinataires
                )
            );

            return $this->redirectToRoute('volontariat_admin_message_new');
        }

        return $this->render(
            'admin/message/new.html.twig',
            array(
                'message' => $message,
                'query' => $query,
                'destinataires' => $destinataires,
                'form' => $form->createView(),
            )
        );
    }
}
