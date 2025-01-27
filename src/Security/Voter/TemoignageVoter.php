<?php

namespace AcMarche\Volontariat\Security\Voter;

use AcMarche\Volontariat\Entity\Security\User;
use AcMarche\Volontariat\Entity\Temoignage;
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
class TemoignageVoter extends Voter
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
        return $subject instanceof Temoignage && in_array($attribute, [self::SHOW, self::EDIT, self::DELETE]);
    }

    /**
     * {@inheritdoc}
     */
    protected function voteOnAttribute($attribute, $temoignage, TokenInterface $token):bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        if ($this->decisionManager->decide($token, [SecurityData::getRoleAdmin()])) {
            return true;
        }
        return match ($attribute) {
            self::SHOW => $this->canView($temoignage, $token),
            self::EDIT => $this->canEdit($temoignage, $token),
            self::DELETE => $this->canDelete($temoignage, $token),
            default => false,
        };
    }

    /**
     * Voir dans l'admin
     */
    private function canView(Temoignage $temoignage, TokenInterface $token): bool
    {
        return (bool) $this->canEdit($temoignage, $token);
    }

    private function canEdit(Temoignage $temoignage, TokenInterface $token): bool
    {
        $user = $token->getUser();
        $associationUser = $temoignage->getUser();

        return $user === $associationUser;
    }

    private function canDelete(Temoignage $temoignage, TokenInterface $token): bool
    {
        return (bool) $this->canEdit($temoignage, $token);
    }
}
