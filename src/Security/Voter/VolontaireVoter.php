<?php

namespace AcMarche\Volontariat\Security\Voter;

use AcMarche\Volontariat\Entity\Association;
use AcMarche\Volontariat\Entity\Volontaire;
use AcMarche\Volontariat\Security\SecurityData;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class VolontaireVoter extends Voter
{
    public const INDEX = 'index';

    public const SHOW = 'show';

    public const EDIT = 'edit';

    public const DELETE = 'delete';

    public function __construct(
        private AccessDecisionManagerInterface $accessDecisionManager,
    ) {
    }

    protected function supports($attribute, $subject): bool
    {
        if ($subject && !$subject instanceof Volontaire) {
            return false;
        }

        return in_array(
            $attribute,
            [self::INDEX, self::SHOW, self::EDIT, self::DELETE]
        );
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
            self::INDEX => $this->canIndex($token),
            self::SHOW => $this->canView($subject, $token),
            self::EDIT => $this->canEdit($subject, $token),
            self::DELETE => $this->canDelete($subject, $token),
            default => false,
        };
    }

    private function canIndex(TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof Association) {
            return false;
        }

        return (bool)$user->valider;
    }

    private function canView(Volontaire $volontaire, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if ($this->canEdit($volontaire, $token)) {
            return true;
        }

        if (!$user instanceof Association) {
            return false;
        }

        return (bool)$user->valider;
    }

    private function canEdit(Volontaire $volontaire, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof Volontaire) {
            return false;
        }

        return $user->getId() === $volontaire->getId();
    }

    private function canDelete(Volontaire $volontaire, TokenInterface $token): bool
    {
        return $this->canEdit($volontaire, $token);
    }
}
