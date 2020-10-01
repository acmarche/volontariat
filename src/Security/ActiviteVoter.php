<?php

namespace AcMarche\Volontariat\Security;

use AcMarche\Volontariat\Entity\Security\User;
use AcMarche\Volontariat\Entity\Activite;
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
        return $subject instanceof Activite && in_array($attribute, [self::SHOW, self::EDIT, self::DELETE]);
    }

    /**
     * {@inheritdoc}
     */
    protected function voteOnAttribute($attribute, $activite, TokenInterface $token)
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
                return $this->canView($activite, $token);
            case self::EDIT:
                return $this->canEdit($activite, $token);
            case self::DELETE:
                return $this->canDelete($activite, $token);
        }

        return false;
    }

    /**
     * Voir dans l'admin
     * @param Activite $activite
     * @param TokenInterface $token
     * @return bool
     */
    private function canView(Activite $activite, TokenInterface $token)
    {
        if ($this->canEdit($activite, $token)) {
            return true;
        }
        return false;
    }

    private function canEdit(Activite $activite, TokenInterface $token)
    {
        $user = $token->getUser();
        $associationUser = $activite->getAssociation()->getUser();

        return $user === $associationUser;
    }

    private function canDelete(Activite $activite, TokenInterface $token)
    {
        if ($this->canEdit($activite, $token)) {
            return true;
        }

        return false;
    }
}
