<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 12/02/18
 * Time: 10:32
 */

namespace AcMarche\Volontariat\Service;

use AcMarche\Volontariat\Entity\Association;
use AcMarche\Volontariat\Entity\Security\User;
use AcMarche\Volontariat\Entity\Volontaire;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class MessageService
{
    private SessionInterface $session;

    public function __construct(
        private EntityManagerInterface $em,
        RequestStack $requestStack,
        private VolontaireService $volontaireService,
        private AssociationService $associationService
    ) {
        $this->session = $requestStack->getSession();
    }

    public function getDestinataires($query, $isSelect = false)
    {
        $key = null;
        $args = [];

        switch ($query) {
            case 'association':
                $key = VolontariatConstante::ASSOCIATION_ADMIN_SEARCH;
                $repository = $this->em->getRepository(Association::class);
                break;
            case 'volontaire':
                $key = VolontariatConstante::VOLONTAIRE_ADMIN_SEARCH;
                $repository = $this->em->getRepository(Volontaire::class);
                break;
            default:
                $repository = false;
                break;
        }

        if (!$repository) {
            return [];
        }

        if ($this->session->has($key)) {
            $args = unserialize($this->session->get($key));
        }

        //si selection_destinataires
        if ($isSelect) {
            return $repository->findBy(['valider' => true]);
        }

        return $repository->search($args);
    }

    /**
     * @param $entity Volontaire|Association
     * @return string|null
     */
    public function getEmailEntity($entity)
    {
        if ($entity->getEmail()) {
            return $entity->getEmail();
        }

        if ($entity->getUser()) {
            return $entity->getUser()->getEmail();
        }

        return null;
    }

    /**
     * @param $entities Volontaire[]|Association[]
     * @return string[]
     */
    public function getEmails($entities): array
    {
        $emails = [];
        foreach ($entities as $entity) {
            $emails[] = $this->getEmailEntity($entity);
        }

        return $emails;
    }

    /**
     * @param Association[] $associations
     */
    public function getFroms(User $user, $associations): array
    {
        $froms = [];
        $froms[$user->getEmail()] = $user->getEmail();
        foreach ($associations as $association) {
            $froms[$association->getEmail()] = $association->getEmail();
        }

        return $froms;
    }

    public function getNom(User $user): string
    {
        $volontaires = $this->volontaireService->getVolontairesByUser($user);
        if ((is_countable($volontaires) ? count($volontaires) : 0) > 0) {
            return $volontaires[0]->getName().' '.$volontaires[0]->getSurname();
        }
        $associations = $this->associationService->getAssociationsByUser($user);
        if ($associations !== []) {
            return $associations[0]->getNom();
        }

        return '';
    }

    /**
     * @param $entity Volontaire|Association
     */
    public function getUser($entity): ?User
    {
        return $entity->getUser();
    }
}
