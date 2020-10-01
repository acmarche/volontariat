<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 12/02/18
 * Time: 10:17
 */

namespace AcMarche\Volontariat\Service;

use AcMarche\Volontariat\Entity\Security\User;
use AcMarche\Volontariat\Entity\Association;
use AcMarche\Volontariat\Repository\AssociationRepository;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class AssociationService
{
    /**
     * @var AssociationRepository
     */
    private $associationRepository;
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    public function __construct(
        AssociationRepository $associationRepository,
        AuthorizationCheckerInterface $authorizationChecker
    ) {
        $this->associationRepository = $associationRepository;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * @param User $user
     * @param bool $valider
     * @return Association[]
     */
    public function getAssociationsByUser(User $user, $valider = false)
    {
        $args = [];
        $args['user'] = $user;
        if ($valider) {
            $args['valider'] = true;
        }

        return $this->associationRepository->findBy($args);
    }

    /**
     * @param User $user
     * @return bool
     */
    public function hasValidAssociation(User $user)
    {
        if ($this->authorizationChecker->isGranted('ROLE_VOLONTARIAT')) {
            if (count($this->getAssociationsByUser($user, true)) > 0) {
                return true;
            }
        }

        return false;
    }
}
