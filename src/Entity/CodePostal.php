<?php

namespace AcMarche\Volontariat\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AcMarche\Volontariat\Repository\CodePostalRepository")
 * @ORM\Table(name="code_postal")
 *
 */
class CodePostal
{
    /**
     * @var integer|null $id
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     */
    protected $id;

    /**
     * @var string|null $nom
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Assert\NotBlank()
     */
    protected $nom;

    /**
     *
     * @ORM\Column(type="integer", nullable=false)
     *
     * @var integer|null $code
     */
    protected $code;

    /**
     * @var string|null $is_commune
     *
     * @ORM\Column(type="string", length=255)
     *
     */
    protected $is_commune;

    /**
     * @var string|null $province
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     */
    protected $province;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getNom(): string
    {
        return $this->nom;
    }

    /**
     * @param string $nom
     */
    public function setNom(string $nom): void
    {
        $this->nom = $nom;
    }

    /**
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * @param int $code
     */
    public function setCode(int $code): void
    {
        $this->code = $code;
    }

    /**
     * @return string
     */
    public function getisCommune(): string
    {
        return $this->is_commune;
    }

    /**
     * @param string $is_commune
     */
    public function setIsCommune(string $is_commune): void
    {
        $this->is_commune = $is_commune;
    }

    /**
     * @return string
     */
    public function getProvince(): string
    {
        return $this->province;
    }

    /**
     * @param string $province
     */
    public function setProvince(string $province): void
    {
        $this->province = $province;
    }
}
