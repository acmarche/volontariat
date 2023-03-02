<?php

namespace AcMarche\Volontariat\Security\Voter;

use AcMarche\Volontariat\Entity\Activite;
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
class ActiviteVoter extends Voter
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
        return $subject instanceof Activite && in_array($attribute, [self::SHOW, self::EDIT, self::DELETE]);
    }

    /**
     * {@inheritdoc}
     */
    protected function voteOnAttribute($attribute, $activite, TokenInterface $token):bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        if ($this->decisionManager->decide($token, ['ROLE_VOLONTARIAT_ADMIN'])) {
            return true;
        }
        return match ($attribute) {
            self::SHOW => $this->canView($activite, $token),
            self::EDIT => $this->canEdit($activite, $token),
            self::DELETE => $this->canDelete($activite, $token),
            default => false,
        };
    }

    /**
     * Voir dans l'admin
     */
    private function canView(Activite $activite, TokenInterface $token): bool
    {
        return (bool) $this->canEdit($activite, $token);
    }

    private function canEdit(Activite $activite, TokenInterface $token): bool
    {
        $user = $token->getUser();
        $associationUser = $activite->getAssociation()->getUser();

        return $user === $associationUser;
    }

    private function canDelete(Activite $activite, TokenInterface $token): bool
    {
        return (bool) $this->canEdit($activite, $token);
    }
}
