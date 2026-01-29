<?php

namespace AcMarche\Volontariat\Controller;

use AcMarche\Volontariat\Controller\Backend\getAssociationTrait;
use AcMarche\Volontariat\Entity\Association;
use AcMarche\Volontariat\Entity\Secteur;
use AcMarche\Volontariat\Entity\Volontaire;
use AcMarche\Volontariat\Form\Contact\ContactType;
use AcMarche\Volontariat\Mailer\MailerContact;
use AcMarche\Volontariat\Repository\VolontaireRepository;
use AcMarche\Volontariat\Security\RolesEnum;
use AcMarche\Volontariat\Spam\Handler\SpamHandler;
use Exception;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ContactController extends AbstractController
{
    use getAssociationTrait;

    public function __construct(
        private readonly VolontaireRepository $volontaireRepository,
        private readonly MailerContact $mailerContact,
        private readonly SpamHandler $spamHandler,
    ) {
    }

    #[Route(path: '/contact/', name: 'volontariat_contact')]
    public function contact(Request $request): Response
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            if ($this->validateForm($request, $data)) {
                try {
                    $this->addFlash('success', 'Votre demande a bien été envoyée');
                    $this->mailerContact->sendContact($data);

                    return $this->redirectToRoute('volontariat_home');
                } catch (TransportExceptionInterface|Exception $e) {
                    $this->addFlash('danger', "Erreur lors de l'envoie du mail");
                }
            }
        }

        return $this->render('@Volontariat/contact/contact.html.twig', ['form' => $form]);
    }

    #[Route(path: '/contact/volontaire/{uuid}', name: 'volontariat_contact_volontaire')]
    #[IsGranted('show', subject: 'volontaire')]
    public function volontaire(
        Request $request,
        #[MapEntity(expr: 'repository.findOneByUuid(uuid)')] Volontaire $volontaire,
    ): Response {
        $form = $this->createForm(ContactType::class, null);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            if ($this->validateForm($request, $data)) {
                try {
                    $this->addFlash('success', 'Le volontaire a bien été contacté');
                    $this->mailerContact->sendToVolontaire($volontaire, $data);
                } catch (TransportExceptionInterface|Exception $e) {
                    $this->addFlash('danger', "Erreur lors de l'envoie du mail");
                }
            }

            return $this->redirectToRoute('volontariat_volontaire_show', ['uuid' => $volontaire->getUuid()]);
        }

        return $this->render(
            '@Volontariat/contact/volontaire.html.twig',
            [
                'volontaire' => $volontaire,
                'form' => $form,
            ],
        );
    }

    #[Route(path: '/contact/volontaire/by/secteur/{id}', name: 'volontariat_contact_volontaire_by_secteur')]
    #[IsGranted(RolesEnum::volontaire->value)]
    public function volontairesBySecteur(
        Request $request,
        Secteur $secteur,
    ): Response {

        if (($hasAssociation = $this->hasAssociation()) instanceof Response) {
            return $hasAssociation;
        }

        $form = $this->createForm(ContactType::class, null);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            if ($this->validateForm($request, $data)) {
                try {
                    $this->addFlash('success', 'Les volontaires ont bien été contacté');
                    $this->mailerContact->sendToVolontairesBySecteur($this->association, $secteur, $data);
                } catch (TransportExceptionInterface|Exception $e) {
                    $this->addFlash('danger', "Erreur lors de l'envoie du mail");
                }
            }

            return $this->redirectToRoute('volontariat_volontaire');
        }

        $volontaires = $this->volontaireRepository->findVolontaireBySecteur($secteur);

        return $this->render(
            '@Volontariat/contact/volontaires.html.twig',
            [
                'volontaires' => $volontaires,
                'form' => $form,
            ],
        );
    }

    #[Route(path: '/contact/association/{slug}', name: 'volontariat_contact_association')]
    public function association(
        Request $request,
        #[MapEntity(expr: 'repository.findOneBySlug(slug)')] Association $association
    ): Response {
        $form = $this->createForm(ContactType::class, null);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            if ($this->validateForm($request, $data)) {
                try {
                    $this->addFlash('success', 'L\'association a bien été contactée');
                    $this->mailerContact->sendToAssociation($association, $data);
                } catch (TransportExceptionInterface|Exception $e) {
                    $this->addFlash('danger', "Erreur lors de l'envoie du mail");
                }
            }

            return $this->redirectToRoute('volontariat_association_show', ['slug' => $association->getSlug()]);
        }

        return $this->render(
            '@Volontariat/contact/association.html.twig',
            [
                'association' => $association,
                'form' => $form,
            ],
        );
    }

    private function validateForm(Request $request, array $data): bool
    {
        if (!$this->spamHandler->isAccepted($request)) {
            $this->addFlash('danger', 'Nombre maximum de contact envoyés.');

            return false;
        }

        if (!$this->spamHandler->checkCaptcha($data['captcha'])) {
            $this->addFlash('danger', 'Vous n\'avez pas sélectionné le chat :-(');

            return false;
        }

        return true;
    }
}
