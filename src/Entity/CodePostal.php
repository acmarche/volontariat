<?php

namespace AcMarche\Volontariat\Entity;

use Doctrine\DBAL\Types\Types;
use AcMarche\Volontariat\Repository\CodePostalRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CodePostalRepository::class)]
#[ORM\Table(name: 'code_postal')]
class CodePostal
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    protected int $id;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: false)]
    #[Assert\NotBlank]
    protected ?string $nom;

    #[ORM\Column(type: Types::INTEGER, nullable: false)]
    protected int $code;

    #[ORM\Column(type: Types::STRING, length: 255)]
    protected ?string $is_commune;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    protected ?string $province;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): void
    {
        $this->nom = $nom;
    }

    public function getCode(): ?int
    {
        return $this->code;
    }

    public function setCode(int $code): void
    {
        $this->code = $code;
    }

    public function getisCommune(): ?string
    {
        return $this->is_commune;
    }

    public function setIsCommune(string $is_commune): void
    {
        $this->is_commune = $is_commune;
    }

    public function getProvince(): ?string
    {
        return $this->province;
    }

    public function setProvince(string $province): void
    {
        $this->province = $province;
    }
}
