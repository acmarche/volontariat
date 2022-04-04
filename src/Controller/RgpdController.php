<?php

namespace AcMarche\Volontariat\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use AcMarche\Volontariat\Entity\Security\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class RgpdController extends AbstractController
{
    public function __construct(private ManagerRegistry $managerRegistry)
    {
    }
    #[Route(path: '/rgpd/')]
    public function index(MailerInterface $mailer) : Response
    {
        $em = $this->managerRegistry->getManager();
        $user = $this->getUser();
        $message = $this->generateMailInfo($user);
        $mailer->send($message);
        return $this->render(
            '@Volontariat/admin/default/index.html.twig',
            [
            ]
        );
    }

    #[Route(path: '/set', name: 'volontariat_admin_home22')]
    public function setUser(UserPasswordHasherInterface $passwordEncoder) : Response
    {
        $em = $this->managerRegistry->getManager();
        $user = $em->getRepository(User::class)->findOneBy(['email' => 'jf@marche.be']);
        $user->setPassword($passwordEncoder->encodePassword($user, "homer22"));
        $em->flush();
        return $this->render('@Volontariat/admin/default/index.html.twig');
    }

    public function generateMailInfo(User $user): Email
    {
        $web = $this->getParameter('acmarche_volontariat_webpath');
        $webpath = $web.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR;
        $css = $webpath.'bootstrap/css/bootstrap.min.css';

        $message = (new Email('Nouveau site et le rgpd'))
            ->from('volontariat@marche.be')
            ->to($user->getEmail())
            ->bcc('jf@marche.be');

        $marche_cid = $message->embed(
            $message->fromPath($webpath.'images/Marche.png')
        );

        $message
            ->setBody(
                $this->renderView(
                    'admin/emails/mail.html.twig',
                    array(
                        'user' => $user,
                        'css' => file_get_contents($css),
                        'logo' => $marche_cid,
                    )
                )
            )
            ->addPart(
                $this->renderView(
                    'admin/emails/mail.html.twig',
                    array(
                        'user' => $user,
                        'css' => file_get_contents($css),
                        'logo' => $marche_cid,
                    )
                ),
                'text/plain'
            );


        return $message;
    }
}
