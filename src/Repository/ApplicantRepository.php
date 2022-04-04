<?php

namespace AcMarche\Volontariat\Repository;

use AcMarche\Volontariat\Entity\Applicant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Applicant|null find($id, $lockMode = null, $lockVersion = null)
 * @method Applicant|null findOneBy(array $criteria, array $orderBy = null)
 * @method Applicant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ApplicantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Applicant::class);
    }

    public function insert(Applicant $applicant): void
    {
        $this->_em->persist($applicant);
        $this->save();
    }

    public function save(): void
    {
        $this->_em->flush();
    }

    public function remove(Applicant $applicant): void
    {
        $this->_em->remove($applicant);
        $this->save();
    }


}
