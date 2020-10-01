<?php

namespace AcMarche\Volontariat\Controller\Backend;

use AcMarche\Volontariat\Entity\Message;
use AcMarche\Volontariat\Form\Contact\ReferencerType;
use AcMarche\Volontariat\Manager\ContactManager;
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
 * @Route("/backend/referencer")
 */
class ReferencerController extends AbstractController
{
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
        ContactManager $contactManager,
        MailerContact $mailerContact,
        Mailer $mailer,
        CaptchaService $captchaService,
        AssociationService $associationService,
        MessageService $messageService
    ) {
        $this->contactManager = $contactManager;
        $this->mailerContact = $mailerContact;
        $this->captchaService = $captchaService;
        $this->associationService = $associationService;
        $this->messageService = $messageService;
        $this->mailer = $mailer;
    }

    /**
     * @Route("/",name="volontariat_backend_referencer")
     * @IsGranted("ROLE_VOLONTARIAT")
     */
    public function index(Request $request)
    {
        if ($user = $this->getUser()) {
            $this->contactManager->populateFromUser($user);
        }

        $user = $this->getUser();
        $associations = $this->associationService->getAssociationsByUser($user, true);
        $froms = $this->messageService->getFroms($user, $associations);

        $message = new Message();
        $nom = $this->messageService->getNom($user);
        $message->setNom($nom);

        $form = $this->createForm(ReferencerType::class, $message, ['froms' => $froms])
            ->add('Envoyer', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            if (!$this->captchaService->captchaverify($request->get('g-recaptcha-response'))) {
                $this->addFlash('danger', 'Contrôle anti-spam non valide');
            } else {
                $this->mailer->sendReferencer($data, $user);
                $this->addFlash('success', 'Le message a bien été envoyé');
            }

            return $this->redirectToRoute('volontariat_dashboard');
        }

        $keySite = $this->getParameter('acmarche_volontariat_captcha_site_key');

        return $this->render(
            'contact/referencer.html.twig',
            [
                'siteKey' => $keySite,
                'form' => $form->createView(),
            ]
        );
    }


}
