<?php

namespace AcMarche\Volontariat\Entity\Security;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 *
 * @ORM\Entity(repositoryClass="AcMarche\Volontariat\Repository\TokenRepository")
 * @ORM\Table(name="token")
 *
 */
class Token implements TimestampableInterface
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
     * @var string|null $value
     *
     * @ORM\Column(type="string", length=50, unique=true)
     * @Assert\NotBlank()
     */
    protected $value;

    /**
     * @var \DateTime $expire_at
     * @ORM\Column(type="date", nullable=false)
     */
    protected $expire_at;

    /**
     * @var User $user
     * @ORM\OneToOne(targetEntity="AcMarche\Volontariat\Entity\Security\User", inversedBy="token" )
     */
    protected $user;

    public function __toString()
    {
        return $this->value;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return null|string
     */
    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * @param null|string $value
     */
    public function setValue(?string $value): void
    {
        $this->value = $value;
    }

    /**
     * @return \DateTime
     */
    public function getExpireAt(): \DateTime
    {
        return $this->expire_at;
    }

    /**
     * @param \DateTime $expire_at
     */
    public function setExpireAt(\DateTime $expire_at): void
    {
        $this->expire_at = $expire_at;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }
}
