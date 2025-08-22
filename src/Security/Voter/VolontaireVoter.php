<?php

namespace AcMarche\Volontariat\Security\Voter;

use AcMarche\Volontariat\Entity\Security\User;
use AcMarche\Volontariat\Entity\Volontaire;
use AcMarche\Volontariat\Security\SecurityData;
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
class VolontaireVoter extends Voter
{
    // Defining these constants is overkill for this simple application, but for real
    // applications, it's a recommended practice to avoid relying on "magic strings"
    public const INDEX = 'index';

    public const SHOW = 'show';

    public const EDIT = 'edit';

    public const DELETE = 'delete';

    public function __construct(
        private AccessDecisionManagerInterface $accessDecisionManager,
    ) {
    }

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
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
        /**
         * @var User $user
         */
        $user = $token->getUser();
        if (!$user) {
            return false;
        }

        $association = $user->association;
        if (!$association) {
            return false;
        }

        return (bool) $association->valider;
    }

    /**
     * Voir dans l'admin.
     */
    private function canView(Volontaire $volontaire, TokenInterface $token): bool
    {
        if ($this->accessDecisionManager->decide($token, [SecurityData::getRoleAssociation()])) {
            return true;
        }

        return $this->canEdit($volontaire, $token);
    }

    private function canEdit(Volontaire $volontaire, TokenInterface $token): bool
    {
        $user = $token->getUser();

        return $user === $volontaire->user;
    }

    private function canDelete(Volontaire $volontaire, TokenInterface $token): bool
    {
        return $this->canEdit($volontaire, $token);
    }
}
