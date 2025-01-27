<?php

namespace AcMarche\Volontariat\Security\Voter;

use AcMarche\Volontariat\Entity\Besoin;
use AcMarche\Volontariat\Entity\Security\User;
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
class BesoinVoter extends Voter
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
    protected function supports($attribute, $subject): bool
    {
        // this voter is only executed for three specific permissions on Post objects
        return $subject instanceof Besoin && in_array($attribute, [self::SHOW, self::EDIT, self::DELETE]);
    }

    /**
     * {@inheritdoc}
     */
    protected function voteOnAttribute($attribute, $besoin, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        if ($this->decisionManager->decide($token, [SecurityData::getRoleAdmin()])) {
            return true;
        }

        return match ($attribute) {
            self::SHOW => $this->canView($besoin, $token),
            self::EDIT => $this->canEdit($besoin, $token),
            self::DELETE => $this->canDelete($besoin, $token),
            default => false,
        };
    }

    /**
     * Voir dans l'admin.
     */
    private function canView(Besoin $besoin, TokenInterface $token): bool
    {
        return (bool) $this->canEdit($besoin, $token);
    }

    private function canEdit(Besoin $besoin, TokenInterface $token): bool
    {
        $user = $token->getUser();
        $associationUser = $besoin->getAssociation()->user;

        return $user === $associationUser;
    }

    private function canDelete(Besoin $besoin, TokenInterface $token): bool
    {
        return (bool) $this->canEdit($besoin, $token);
    }
}
