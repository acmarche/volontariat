<?php

namespace AcMarche\Volontariat\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use AcMarche\Volontariat\Entity\Applicant;
use AcMarche\Volontariat\Entity\Association;
use AcMarche\Volontariat\Form\ApplicantType;
use AcMarche\Volontariat\Repository\ApplicantRepository;
use AcMarche\Volontariat\Service\Mailer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class RegisterController
 * @package AcMarche\Admin\Security\Controller
 */
#[Route(path: '/demandeur')]
class ApplicantController extends AbstractController
{
    public function __construct(private ApplicantRepository $applicantRepository, private Mailer $mailer, private ManagerRegistry $managerRegistry)
    {
    }
    /**
     * Displays a form to create a new Applicant applicant.
     *
     *
     */
    #[Route(path: '/', name: 'volontariatapplicant_new', methods: ['GET', 'POST'])]
    public function newAction(Request $request) : Response
    {
        $applicant = new Applicant();
        $form = $this->createForm(ApplicantType::class, $applicant)
            ->add('submit', SubmitType::class, array('label' => 'Create'));
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->managerRegistry->getManager();
            $em->persist($applicant);
            $em->flush();
            $this->addFlash("success", "Vos coordonnées ont bien été enregistrées");

            $this->mailer->send(
                "volontariat@marche.be",
                "volontariat@marche.be",
                $applicant->getSurname()." ".$applicant->getName()." demande de l'aide",
                "Consulter les demandes sur l'adresse " . $this->generateUrl(
                    'volontariat_admin_applicant',
                    [],
                    UrlGeneratorInterface::ABSOLUTE_URL
                )
            );

            return $this->redirectToRoute('volontariat_home');
        }
        return $this->render(
            '@Volontariat/applicant/new.html.twig',
            array(
                'applicant' => $applicant,
                'form' => $form->createView(),
            )
        );
    }
}
