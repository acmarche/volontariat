<?php

namespace AcMarche\Volontariat\Entity;

use AcMarche\Volontariat\Entity\Enum\StatisticTypeEnum;
use AcMarche\Volontariat\Repository\StatisticRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'statistic')]
#[ORM\Entity(repositoryClass: StatisticRepository::class)]
class Statistic
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    public int $id;

    #[ORM\Column(type: Types::STRING, enumType: StatisticTypeEnum::class)]
    public StatisticTypeEnum $type;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    public DateTimeImmutable $createdAt;

    #[ORM\ManyToOne(targetEntity: Association::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    public ?Association $association = null;

    #[ORM\ManyToOne(targetEntity: Volontaire::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    public ?Volontaire $volontaire = null;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
    }
}
