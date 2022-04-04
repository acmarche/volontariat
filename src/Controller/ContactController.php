<?php

namespace AcMarche\Volontariat\Controller;

use Symfony\Component\HttpFoundation\Response;
use AcMarche\Volontariat\Entity\Association;
use AcMarche\Volontariat\Entity\Volontaire;
use AcMarche\Volontariat\Form\Contact\ContactBaseType;
use AcMarche\Volontariat\Form\Contact\ContactVolontaireType;
use AcMarche\Volontariat\Manager\ContactManager;
use AcMarche\Volontariat\Repository\VolontaireRepository;
use AcMarche\Volontariat\Service\AssociationService;
use AcMarche\Volontariat\Service\CaptchaService;
use AcMarche\Volontariat\Service\Mailer;
use AcMarche\Volontariat\Service\MailerContact;
use AcMarche\Volontariat\Service\MessageService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ContactController
 * @package AcMarche\Volontariat\Controller
 */
#[Route(path: '/contact')]
class ContactController extends AbstractController
{
    public function __construct(private VolontaireRepository $volontaireRepository, private ContactManager $contactManager, private MailerContact $mailerContact, private Mailer $mailer, private CaptchaService $captchaService, private AssociationService $associationService, private MessageService $messageService)
    {
    }
    #[Route(path: '/volontaire/{id}', name: 'volontariat_contact_volontaire')]
    #[IsGranted('show', subject: 'volontaire')]
    public function volontaire(Request $request, Volontaire $volontaire) : Response
    {
        if (($user = $this->getUser()) !== null) {
            $this->contactManager->populateFromUser($user);
        }
        $this->contactManager->setDestinataire($volontaire->getEmail());
        $form = $this->createForm(ContactVolontaireType::class, $this->contactManager)
            ->add('Envoyer', SubmitType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            if (!$this->captchaService->captchaverify($request->get('g-recaptcha-response'))) {
                $this->addFlash('danger', 'Contrôle anti-spam non valide');
            } else {
                $this->mailerContact->sendToVolontaire($volontaire, $data);
                $this->addFlash('success', 'Le volontaire a bien été contacté');
            }

            return $this->redirectToRoute('volontariat_volontaire_show', ['id' => $volontaire->getId()]);
        }
        $keySite = $this->getParameter('acmarche_volontariat_captcha_site_key');
        return $this->render(
            '@Volontariat/contact/volontaire.html.twig',
            [
                'volontaire' => $volontaire,
                'siteKey' => $keySite,
                'form' => $form->createView(),
            ]
        );
    }
    #[Route(path: '/association/{id}', name: 'volontariat_contact_association')]
    public function association(Request $request, Association $association) : Response
    {
        if (($user = $this->getUser()) !== null) {
            $this->contactManager->populateFromUser($user);
        }
        $this->contactManager->setDestinataire($association->getEmail());
        $form = $this->createForm(ContactBaseType::class, $this->contactManager)
            ->add('Envoyer', SubmitType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            if (!$this->captchaService->captchaverify($request->get('g-recaptcha-response'))) {
                $this->addFlash('danger', 'Contrôle anti-spam non valide');
            } else {
                $this->mailerContact->sendToAssociation($association, $data);
                $this->addFlash('success', 'L\'association a bien été contactée');
            }

            return $this->redirectToRoute('volontariat_association_show', ['id' => $association->getId()]);
        }
        $keySite = $this->getParameter('acmarche_volontariat_captcha_site_key');
        return $this->render(
            '@Volontariat/contact/association.html.twig',
            [
                'association' => $association,
                'siteKey' => $keySite,
                'form' => $form->createView(),
            ]
        );
    }
    #[Route(path: '/captcha', name: 'volontariat_captcha')]
    public function captcha(Request $request) : void
    {
    }
}
