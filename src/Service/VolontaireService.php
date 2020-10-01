<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 12/02/18
 * Time: 10:17
 */

namespace AcMarche\Volontariat\Service;

use AcMarche\Volontariat\Entity\Security\User;
use AcMarche\Volontariat\Entity\Volontaire;
use AcMarche\Volontariat\Repository\AssociationRepository;
use AcMarche\Volontariat\Repository\VolontaireRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class VolontaireService
{


    /**
     * @var VolontaireRepository
     */
    private $volontaireRepository;
    /**
     * @var AssociationRepository
     */
    private $associationRepository;
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;
    /**
     * @var SessionInterface
     */
    private $session;

    public function __construct(
        VolontaireRepository $volontaireRepository,
        AssociationRepository $associationRepository,
        AuthorizationCheckerInterface $authorizationChecker,
        TokenStorageInterface $tokenStorage,
        SessionInterface $session
    ) {
        $this->volontaireRepository = $volontaireRepository;
        $this->associationRepository = $associationRepository;
        $this->authorizationChecker = $authorizationChecker;
        $this->tokenStorage = $tokenStorage;
        $this->session = $session;
    }

    public function getVolontairesByUser(User $user, $valider = false)
    {
        $args = [];
        $args['user'] = $user;
        if ($valider) {
            $args['valider'] = true;
        }

        return $this->volontaireRepository->findBy($args);
    }

    public function hasValidVolontaire(User $user)
    {
        return count($this->getVolontairesByUser($user, true)) > 0;
    }

    public function getAssociationsWithSameSecteur(Volontaire $volontaire)
    {
        $secteurs = $volontaire->getSecteurs();

        return $this->associationRepository->findAssociationBySecteur(
            $secteurs
        );
    }
}
