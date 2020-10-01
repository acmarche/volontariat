<?php

namespace AcMarche\Volontariat\Controller;

use AcMarche\Volontariat\Entity\Association;
use AcMarche\Volontariat\Entity\Message;
use AcMarche\Volontariat\Entity\Volontaire;
use AcMarche\Volontariat\Form\Contact\RecommanderType;
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
 * @Route("/recommander")
 */
class RecommanderController extends AbstractController
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
     * @Route("/volontaire/{id}",name="volontariat_recommander_volontaire")
     * @IsGranted("ROLE_VOLONTARIAT")
     */
    public function recommanderVolontaure(Request $request, Volontaire $volontaire)
    {
        if ($user = $this->getUser()) {
            $this->contactManager->populateFromUser($user);
        }

        $this->contactManager->setDestinataire($volontaire->getEmail());
        $user = $this->getUser();
        $associations = $this->associationService->getAssociationsByUser($user, true);
        $froms = $this->messageService->getFroms($user, $associations);

        $message = new Message();
        $nom = $this->messageService->getNom($user);
        $message->setNom($nom);

        $form = $this->createForm(RecommanderType::class, $message, ['froms' => $froms])
            ->add('Envoyer', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            if (!$this->captchaService->captchaverify($request->get('g-recaptcha-response'))) {
                $this->addFlash('danger', 'Contrôle anti-spam non valide');
            } else {
                $this->mailer->sendRecommanderVolontaire($data, $volontaire, $user);
                $this->addFlash('success', 'Le volontaire a bien été recommandé');
            }

            return $this->redirectToRoute('volontariat_volontaire_show', ['id' => $volontaire->getId()]);
        }

        $keySite = $this->getParameter('acmarche_volontariat_captcha_site_key');

        return $this->render(
            'contact/recommander_volontaire.html.twig',
            [
                'volontaire' => $volontaire,
                'siteKey' => $keySite,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/association/{id}",name="volontariat_recommander_association")
     * @isGranted("ROLE_VOLONTARIAT")
     */
    public function recommanderAssociation(Request $request, Association $association)
    {
        if ($user = $this->getUser()) {
            $this->contactManager->populateFromUser($user);
        }

        $this->contactManager->setDestinataire($association->getEmail());
        $user = $this->getUser();
        $associations = $this->associationService->getAssociationsByUser($user, true);
        $froms = $this->messageService->getFroms($user, $associations);

        $message = new Message();

        $nom = $this->messageService->getNom($user);
        $message->setNom($nom);

        $form = $this->createForm(RecommanderType::class, $message, ['froms' => $froms])
            ->add('Envoyer', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            if (!$this->captchaService->captchaverify($request->get('g-recaptcha-response'))) {
                $this->addFlash('danger', 'Contrôle anti-spam non valide');
            } else {
                $this->mailer->sendRecommanderAssociation($data, $association, $user);
                $this->addFlash('success', 'L\' association a bien été recommandée');
            }

            return $this->redirectToRoute('volontariat_association_show', ['id' => $association->getId()]);
        }

        $keySite = $this->getParameter('acmarche_volontariat_captcha_site_key');

        return $this->render(
            'contact/recommander_associationhtml.twig',
            [
                'association' => $association,
                'siteKey' => $keySite,
                'form' => $form->createView(),
            ]
        );
    }

}
