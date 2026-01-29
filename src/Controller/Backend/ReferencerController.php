<?php

namespace AcMarche\Volontariat\Controller\Backend;

use AcMarche\Volontariat\Entity\Association;
use AcMarche\Volontariat\Entity\Message;
use AcMarche\Volontariat\Entity\Volontaire;
use AcMarche\Volontariat\Form\Contact\ReferencerType;
use AcMarche\Volontariat\Mailer\MailerContact;
use AcMarche\Volontariat\Mailer\MessageService;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use AcMarche\Volontariat\Security\RolesEnum;
class ReferencerController extends AbstractController
{
    use getAssociationTrait;
    public function __construct(
        private MailerContact $mailerContact,
        private MessageService $messageService
    ) {
    }

    #[Route(path: '/referencer/volontaire/{uuid}', name: 'volontariat_referencer_volontaire')]
    #[IsGranted(RolesEnum::volontaire->value)]
    public function volontaire(Request $request,#[MapEntity(expr: 'repository.findOneByUuid(uuid)')]  Volontaire $volontaire): Response
    {
        if (($hasAssociation = $this->hasAssociation()) instanceof Response) {
            return $hasAssociation;
        }

        $message = new Message();
        $message->nom = $this->association->name;
        $message->sujet = $this->association->name;
        $message->from = $this->association->email;

        $form = $this->createForm(ReferencerType::class, $message);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            try {
                $this->mailerContact->sendReferencerVolontaire($this->association, $volontaire, $data);
                $this->addFlash('success', 'Le message a bien été envoyé');
            } catch (TransportExceptionInterface $e) {
                $this->addFlash('danger', "Erreur lors de l'envoie: ".$e->getMessage());
            }

            return $this->redirectToRoute('volontariat_volontaire');
        }

        return $this->render(
            '@Volontariat/contact/referencer_volontaire.html.twig',
            [
                'form' => $form,
                'volontaire' => $volontaire,
            ]
        );
    }

    #[Route(path: '/referencer/association/{slug}', name: 'volontariat_referencer_association')]
    public function association(Request $request, Association $association): Response
    {
        $user = $this->getUser();

        $message = new Message();
        if ($user) {
            $message->nom = $user->name;
            $message->sujet = $user->name;
            $message->from = $user->email;
        }

        $form = $this->createForm(ReferencerType::class, $message);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            try {
                $this->mailerContact->sendReferencerAssociation($association, $data);
                $this->addFlash('success', 'Le message a bien été envoyé');
            } catch (TransportExceptionInterface $e) {
                $this->addFlash('danger', "Erreur lors de l'envoie: ".$e->getMessage());
            }

            return $this->redirectToRoute('volontariat_association');
        }

        return $this->render(
            '@Volontariat/contact/referencer_association.html.twig',
            [
                'form' => $form,
                'association' => $association,
            ]
        );
    }
}
