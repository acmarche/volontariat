<?php

namespace AcMarche\Volontariat\Security;

use AcMarche\Volontariat\Entity\Security\User;
use AcMarche\Volontariat\Entity\Page;
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
class PageVoter extends Voter
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
        return $subject instanceof Page && in_array($attribute, [self::SHOW, self::EDIT, self::DELETE]);
    }

    /**
     * {@inheritdoc}
     */
    protected function voteOnAttribute($attribute, $page, TokenInterface $token)
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
                return $this->canView($page, $token);
            case self::EDIT:
                return $this->canEdit($page, $token);
            case self::DELETE:
                return $this->canDelete($page, $token);
        }

        return false;
    }

    /**
     * Voir dans l'admin
     * @param Page $page
     * @param TokenInterface $token
     * @return bool
     */
    private function canView(Page $page, TokenInterface $token)
    {
        return true;
    }

    private function canEdit(Page $page, TokenInterface $token)
    {
        return false;
    }

    private function canDelete(Page $page, TokenInterface $token)
    {
        if ($this->canEdit($page, $token)) {
            return true;
        }

        return false;
    }
}
