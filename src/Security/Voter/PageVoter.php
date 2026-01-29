<?php

namespace AcMarche\Volontariat\Security\Voter;

use AcMarche\Volontariat\Entity\Page;
use AcMarche\Volontariat\Security\SecurityData;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

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
    public const SHOW = 'show';

    public const EDIT = 'edit';

    public const DELETE = 'delete';

    public function __construct(private AccessDecisionManagerInterface $accessDecisionManager)
    {
    }

    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject):bool
    {
        // this voter is only executed for three specific permissions on Post objects
        return $subject instanceof Page && in_array($attribute, [self::SHOW, self::EDIT, self::DELETE]);
    }

    /**
     * {@inheritdoc}
     */
    protected function voteOnAttribute($attribute, $page, TokenInterface $token):bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        if ($this->accessDecisionManager->decide($token, [SecurityData::getRoleAdmin()])) {
            return true;
        }

        return match ($attribute) {
            self::SHOW => $this->canView(),
            self::EDIT => $this->canEdit(),
            self::DELETE => $this->canDelete(),
            default => false,
        };
    }

    /**
     * Voir dans l'admin
     */
    private function canView(): bool
    {
        return true;
    }

    private function canEdit(): bool
    {
        return false;
    }

    private function canDelete(): bool
    {
        return $this->canEdit();
    }
}
