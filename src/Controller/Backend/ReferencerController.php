<?php

namespace AcMarche\Volontariat\Controller\Backend;

use AcMarche\Volontariat\Entity\Message;
use AcMarche\Volontariat\Entity\Volontaire;
use AcMarche\Volontariat\Form\Contact\ReferencerType;
use AcMarche\Volontariat\Mailer\MailerContact;
use AcMarche\Volontariat\Mailer\MessageService;
use AcMarche\Volontariat\Service\CaptchaService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/backend/referencer')]
class ReferencerController extends AbstractController
{
    use getAssociationTrait;

    public function __construct(
        private MailerContact $mailerContact,
        private CaptchaService $captchaService,
        private MessageService $messageService
    ) {
    }

    #[Route(path: '/volontaire/{uuid}', name: 'volontariat_backend_volontaire')]
    #[IsGranted('ROLE_VOLONTARIAT')]
    public function index(Request $request, Volontaire $volontaire): Response
    {
        if (($hasAssociation = $this->hasAssociation()) !== null) {
            return $hasAssociation;
        }

        $message = new Message();
        $message->nom = $this->association->name;
        $message->sujet = $this->association->name;
        $message->from = $this->association->email;
        $message->to = $volontaire->email;

        $form = $this->createForm(ReferencerType::class, $message);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            try {
                $this->mailerContact->sendReferencerVolontaire($this->association, $volontaire, $data);
                $this->addFlash('success', 'Le message a bien été envoyé');
            } catch (TransportExceptionInterface $e) {
                $this->addFlash('danger', 'Erreur lors de l\'envoie: '.$e->getMessage());
            }

            return $this->redirectToRoute('volontariat_volontaire');
        }

        return $this->render(
            '@Volontariat/contact/referencer_volontaire.html.twig',
            [
                'form' => $form->createView(),
                'volontaire' => $volontaire,
            ]
        );
    }
}
