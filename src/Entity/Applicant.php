<?php

namespace AcMarche\Volontariat\Entity;

use AcMarche\Volontariat\Entity\Security\User;
use AcMarche\Volontariat\InterfaceDef\Uploadable;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use AcMarche\Volontariat\Validator\Constraints as AcMarcheAssert;

/**
 * Volontaire
 * @ORM\Table(name="applicant")
 * @ORM\Entity(repositoryClass="AcMarche\Volontariat\Repository\ApplicantRepository")
 *
 *
 */
class Applicant  implements TimestampableInterface
{
    use TimestampableTrait;

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
     * @var string|null $name
     *
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    protected $name;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\NotNull()
     * @var string|null $surname
     *
     */
    protected $surname;

    /**
     * @ORM\Column(type="string", nullable=false)
     *
     * @var string|null $city
     *
     */
    protected $city;

    /**
     * @var string|null
     * @ORM\Column(name="email", nullable=true)
     * @Assert\Email(
     *     message = "The email '{{ value }}' is not a valid email."
     * )
     */
    protected $email;

    /**
     * @ORM\Column(type="string", nullable=true )
     *
     * @var string|null $phone
     */
    protected $phone;

    /**
     *
     * @ORM\Column(type="text", nullable=true)
     *
     * @var string|null $description
     */
    protected $description;
    /**
     *
     * @ORM\Column(type="text", nullable=true)
     *
     * @var string|null $notes
     */
    protected $notes;

    public function __toString()
    {
        return $this->getSurname().' '.$this->getName();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(?string $surname): self
    {
        $this->surname = $surname;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): self
    {
        $this->notes = $notes;

        return $this;
    }


}
