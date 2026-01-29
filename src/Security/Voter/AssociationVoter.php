<?php

namespace AcMarche\Volontariat\Security\Voter;

use AcMarche\Volontariat\Entity\Association;
use AcMarche\Volontariat\Security\SecurityData;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class AssociationVoter extends Voter
{
    public const SHOW = 'show';

    public const EDIT = 'edit';

    public const DELETE = 'delete';

    public function __construct(
        private AccessDecisionManagerInterface $accessDecisionManager,
    ) {}

    protected function supports($attribute, $subject): bool
    {
        return $subject instanceof Association && in_array($attribute, [self::SHOW, self::EDIT, self::DELETE]);
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        if ($this->accessDecisionManager->decide($token, [SecurityData::getRoleAdmin()])) {
            return true;
        }

        return match ($attribute) {
            self::SHOW => $this->canView($subject, $token),
            self::EDIT => $this->canEdit($subject, $token),
            self::DELETE => $this->canDelete($subject, $token),
            default => false,
        };
    }

    private function canView(Association $association, TokenInterface $token): bool
    {
        return $this->canEdit($association, $token);
    }

    private function canEdit(Association $association, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof Association) {
            return false;
        }

        return $user->getId() === $association->getId();
    }

    private function canDelete(Association $association, TokenInterface $token): bool
    {
        return $this->canEdit($association, $token);
    }
}
