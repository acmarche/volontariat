<?php

namespace AcMarche\Volontariat\Security\Voter;

use AcMarche\Volontariat\Entity\Security\User;
use AcMarche\Volontariat\Entity\Volontaire;
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

    private ?User $user = null;

    public function __construct(
        private AccessDecisionManagerInterface $decisionManager,
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
    protected function voteOnAttribute($attribute, $volontaire, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        $this->user = $user;

        if ($this->decisionManager->decide($token, ['ROLE_VOLONTARIAT_ADMIN'])) {
            return true;
        }

        return match ($attribute) {
            self::INDEX => $this->canIndex(),
            self::SHOW => $this->canView($volontaire, $token),
            self::EDIT => $this->canEdit($volontaire, $token),
            self::DELETE => $this->canDelete($volontaire, $token),
            default => false,
        };
    }

    private function canIndex(): bool
    {
        return $this->associationService->hasValidAssociation($this->user);
    }

    /**
     * Voir dans l'admin
     */
    private function canView(Volontaire $volontaire, TokenInterface $token): bool
    {
        if ($this->canEdit($volontaire, $token)) {
            return true;
        }

        return $this->associationService->hasValidAssociation($this->user);
    }

    private function canEdit(Volontaire $volontaire, TokenInterface $token): bool
    {
        $user = $token->getUser();

        return $user === $volontaire->getUser();
    }

    private function canDelete(Volontaire $volontaire, TokenInterface $token): bool
    {
        return (bool)$this->canEdit($volontaire, $token);
    }


    public function hasValidVolontaire(User $user): bool
    {
        return (is_countable($this->volontaireRepository->getVolontairesByUser($user, true)) ? count($this->volontaireRepository->getVolontairesByUser($user, true)) : 0) > 0;
    }

    public function getAssociationsWithSameSecteur(Volontaire $volontaire)
    {
        $secteurs = $volontaire->getSecteurs();

        return $this->associationRepository->findAssociationBySecteur(
            $secteurs
        );
    }

}
