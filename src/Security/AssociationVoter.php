<?php

namespace AcMarche\Volontariat\Security;

use AcMarche\Volontariat\Entity\Security\User;
use AcMarche\Volontariat\Entity\Association;
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
    const SHOW = 'show';
    const EDIT = 'edit';
    const DELETE = 'delete';
    private $decisionManager;

    public function __construct(AccessDecisionManagerInterface $decisionManager)
    {
        $this->decisionManager = $decisionManager;
    }

    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        // this voter is only executed for three specific permissions on Post objects
        return $subject instanceof Association && in_array($attribute, [self::SHOW, self::EDIT, self::DELETE]);
    }

    /**
     * {@inheritdoc}
     */
    protected function voteOnAttribute($attribute, $association, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        if ($this->decisionManager->decide($token, ['ROLE_VOLONTARIAT_ADMIN'])) {
            return true;
        }

        switch ($attribute) {
            case self::SHOW:
                return $this->canView($association, $token);
            case self::EDIT:
                return $this->canEdit($association, $token);
            case self::DELETE:
                return $this->canDelete($association, $token);
        }

        return false;
    }

    /**
     * Voir dans l'admin
     * @param Association $association
     * @param TokenInterface $token
     * @return bool
     */
    private function canView(Association $association, TokenInterface $token)
    {
        if ($this->canEdit($association, $token)) {
            return true;
        }
        return false;
    }

    private function canEdit(Association $association, TokenInterface $token)
    {
        $user = $token->getUser();

        return $user === $association->getUser();
    }

    private function canDelete(Association $association, TokenInterface $token)
    {
        if ($this->canEdit($association, $token)) {
            return true;
        }

        return false;
    }
}
