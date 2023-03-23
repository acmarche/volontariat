<?php

namespace AcMarche\Volontariat\Controller\Backend;

use AcMarche\Volontariat\Entity\Message;
use AcMarche\Volontariat\Form\Contact\ReferencerType;
use AcMarche\Volontariat\Mailer\Mailer;
use AcMarche\Volontariat\Mailer\MessageService;
use AcMarche\Volontariat\Service\CaptchaService;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/backend/referencer')]
class ReferencerController extends AbstractController
{
    public function __construct(
        private Mailer $mailer,
        private CaptchaService $captchaService,
        private MessageService $messageService
    ) {
    }

    #[Route(path: '/', name: 'volontariat_backend_referencer')]
    #[IsGranted('ROLE_VOLONTARIAT')]
    public function index(Request $request): Response
    {
        if (($user = $this->getUser()) !== null) {
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
            '@Volontariat/contact/referencer.html.twig',
            [
                'siteKey' => $keySite,
                'form' => $form->createView(),
            ]
        );
    }
}
