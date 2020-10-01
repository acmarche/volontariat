<?php

namespace AcMarche\Volontariat\Controller;

use AcMarche\Volontariat\Entity\Activite;
use AcMarche\Volontariat\Entity\Association;
use AcMarche\Volontariat\Entity\Besoin;
use AcMarche\Volontariat\Entity\Page;
use AcMarche\Volontariat\Entity\Security\User;
use AcMarche\Volontariat\Entity\Volontaire;
use AcMarche\Volontariat\Service\FileHelper;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RgpdController extends AbstractController
{
    /**
     * @Route("/rgpd/")
     *
     */
    public function index(\Swift_Mailer $mailer)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $message = $this->generateMailInfo($user);
        $mailer->send($message);

        return $this->render(
            '@Volontariat/admin/default/index.html.twig',
            [
            ]
        );
    }

    /**
     * @Route("/set", name="volontariat_admin_home22")
     *
     */
    public function setUser(UserPasswordEncoderInterface $passwordEncoder)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->findOneBy(['email' => 'jf@marche.be']);
        $user->setPassword($passwordEncoder->encodePassword($user, "homer22"));
        $em->flush();

        return $this->render('@Volontariat/admin/default/index.html.twig');
    }

    public function generateMailInfo(User $user): \Swift_Message
    {
        $web = $this->getParameter('acmarche_volontariat_webpath');
        $webpath = $web.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR;
        $css = $webpath.'bootstrap/css/bootstrap.min.css';

        $message = (new \Swift_Message('Nouveau site et le rgpd'))
            ->setFrom('volontariat@marche.be')
            ->setTo($user->getEmail())
            ->setBcc('jf@marche.be');

        $marche_cid = $message->embed(
            \Swift_Image::fromPath($webpath.'images/Marche.png')
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
                ),
                'text/html'
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
