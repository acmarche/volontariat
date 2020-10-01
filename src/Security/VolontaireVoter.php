<?php

namespace AcMarche\Volontariat\Security;

use AcMarche\Volontariat\Entity\Security\User;
use AcMarche\Volontariat\Entity\Volontaire;
use AcMarche\Volontariat\Service\AssociationService;
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
    const INDEX = 'index';
    const SHOW = 'show';
    const EDIT = 'edit';
    const DELETE = 'delete';
    private $decisionManager;
    /**
     * @var AssociationService
     */
    private $associationService;

    /**
     * @var User $user
     */
    private $user;

    public function __construct(AccessDecisionManagerInterface $decisionManager, AssociationService $associationService)
    {
        $this->decisionManager = $decisionManager;
        $this->associationService = $associationService;
    }

    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        if ($subject) {
            if (!$subject instanceof Volontaire) {
                return false;
            }
        }

        return in_array(
            $attribute,
            [self::INDEX, self::SHOW, self::EDIT, self::DELETE]
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function voteOnAttribute($attribute, $volontaire, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        $this->user = $user;

        if ($this->decisionManager->decide($token, ['ROLE_VOLONTARIAT_ADMIN'])) {
            return true;
        }

        switch ($attribute) {
            case self::INDEX:
                return $this->canIndex();
            case self::SHOW:
                return $this->canView($volontaire, $token);
            case self::EDIT:
                return $this->canEdit($volontaire, $token);
            case self::DELETE:
                return $this->canDelete($volontaire, $token);
        }

        return false;
    }

    private function canIndex()
    {
        return $this->associationService->hasValidAssociation($this->user);
    }

    /**
     * Voir dans l'admin
     * @param Volontaire $volontaire
     * @param TokenInterface $token
     * @return bool
     */
    private function canView(Volontaire $volontaire, TokenInterface $token)
    {
        if ($this->canEdit($volontaire, $token)) {
            return true;
        }

        return $this->associationService->hasValidAssociation($this->user);
    }

    private function canEdit(Volontaire $volontaire, TokenInterface $token)
    {
        $user = $token->getUser();

        return $user === $volontaire->getUser();
    }

    private function canDelete(Volontaire $volontaire, TokenInterface $token)
    {
        if ($this->canEdit($volontaire, $token)) {
            return true;
        }

        return false;
    }
}
