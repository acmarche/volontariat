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
    public function __construct(private AssociationRepository $associationRepository, private AuthorizationCheckerInterface $authorizationChecker)
    {
    }

    /**
     * @param bool $valider
     * @return Association[]
     */
    public function getAssociationsByUser(User $user, $valider = false): array
    {
        $args = [];
        $args['user'] = $user;
        if ($valider) {
            $args['valider'] = true;
        }

        return $this->associationRepository->findBy($args);
    }

    public function hasValidAssociation(User $user): bool
    {
        return $this->authorizationChecker->isGranted('ROLE_VOLONTARIAT') && $this->getAssociationsByUser($user, true) !== [];
    }
}
