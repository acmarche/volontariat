<?php

namespace AcMarche\Volontariat\Controller;

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
 * @Route("/contact")
 */
class ContactController extends AbstractController
{
    /**
     * @var VolontaireRepository
     */
    private $volontaireRepository;
    /**
     * @var ContactManager
     */
    private $contactManager;
    /**
     * @var MailerContact
     */
    private $mailerContact;
    /**
     * @var CaptchaService
     */
    private $captchaService;
    /**
     * @var AssociationService
     */
    private $associationService;
    /**
     * @var MessageService
     */
    private $messageService;
    /**
     * @var Mailer
     */
    private $mailer;

    public function __construct(
        VolontaireRepository $volontaireRepository,
        ContactManager $contactManager,
        MailerContact $mailerContact,
        Mailer $mailer,
        CaptchaService $captchaService,
        AssociationService $associationService,
        MessageService $messageService
    ) {
        $this->volontaireRepository = $volontaireRepository;
        $this->contactManager = $contactManager;
        $this->mailerContact = $mailerContact;
        $this->captchaService = $captchaService;
        $this->associationService = $associationService;
        $this->messageService = $messageService;
        $this->mailer = $mailer;
    }

    /**
     * @Route("/volontaire/{id}",name="volontariat_contact_volontaire")
     * @IsGranted("show", subject="volontaire")
     */
    public function volontaire(Request $request, Volontaire $volontaire)
    {
        if ($user = $this->getUser()) {
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
            'contact/volontaire.html.twig',
            [
                'volontaire' => $volontaire,
                'siteKey' => $keySite,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/association/{id}",name="volontariat_contact_association")
     *
     */
    public function association(Request $request, Association $association)
    {
        if ($user = $this->getUser()) {
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
            'contact/association.html.twig',
            [
                'association' => $association,
                'siteKey' => $keySite,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/captcha",name="volontariat_captcha")
     *
     */
    public function captcha(Request $request)
    {


    }

}
