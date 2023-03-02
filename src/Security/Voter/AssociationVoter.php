<?php

namespace AcMarche\Volontariat\Security\Voter;

use AcMarche\Volontariat\Entity\Association;
use AcMarche\Volontariat\Entity\Security\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * It grants or denies permissions for actions related to blog posts (such as
 * showing, editing and deleting posts).
 *
 * See http://symfony.com/doc/current/security/voters.html
 *
 * @author Yonel Ceruto <yonelceruto@gmail.com>
 */
class AssociationVoter extends Voter
{
    // Defining these constants is overkill for this simple application, but for real
    // applications, it's a recommended practice to avoid relying on "magic strings"
    public const SHOW = 'show';
    public const EDIT = 'edit';
    public const DELETE = 'delete';

    public function __construct(private AccessDecisionManagerInterface $decisionManager)
    {
    }

    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject):bool
    {
        // this voter is only executed for three specific permissions on Post objects
        return $subject instanceof Association && in_array($attribute, [self::SHOW, self::EDIT, self::DELETE]);
    }

    /**
     * {@inheritdoc}
     */
    protected function voteOnAttribute($attribute, $association, TokenInterface $token):bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        if ($this->decisionManager->decide($token, ['ROLE_VOLONTARIAT_ADMIN'])) {
            return true;
        }
        return match ($attribute) {
            self::SHOW => $this->canView($association, $token),
            self::EDIT => $this->canEdit($association, $token),
            self::DELETE => $this->canDelete($association, $token),
            default => false,
        };
    }

    /**
     * Voir dans l'admin
     */
    private function canView(Association $association, TokenInterface $token): bool
    {
        return (bool) $this->canEdit($association, $token);
    }

    private function canEdit(Association $association, TokenInterface $token): bool
    {
        $user = $token->getUser();

        return $user === $association->getUser();
    }

    private function canDelete(Association $association, TokenInterface $token): bool
    {
        return (bool) $this->canEdit($association, $token);
    }

      protected function canAccess(): bool
    {
        $user = $this->getUser();
        if ($user->hasRole('ROLE_VOLONTARIAT_ADMIN')) {
            return true;
        }

        return $this->hasValidAssociation($user);
    }

     public function hasValidAssociation(User $user): bool
    {
        return $this->authorizationChecker->isGranted('ROLE_VOLONTARIAT') && $this->associationRepository->getAssociationsByUser($user,true)(
                $user,
                true
            ) !== [];
    }
}
