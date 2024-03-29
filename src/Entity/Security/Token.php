<?php

namespace AcMarche\Volontariat\Entity\Security;

use AcMarche\Volontariat\Repository\TokenRepository;
use Stringable;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TokenRepository::class)]
#[ORM\Table(name: 'token')]
class Token implements TimestampableInterface, Stringable
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    protected int $id;

    #[ORM\Column(type: 'string', length: 50, unique: true)]
    #[Assert\NotBlank]
    protected string $value;

    #[ORM\Column(type: 'date', nullable: false)]
    protected DateTimeInterface $expire_at;

    #[ORM\OneToOne(targetEntity: User::class, inversedBy: 'token')]
    protected User $user;

    public function __toString(): string
    {
        return $this->value;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): void
    {
        $this->value = $value;
    }

    public function getExpireAt(): DateTimeInterface
    {
        return $this->expire_at;
    }

    /**
     * @param DateTime $expire_at
     */
    public function setExpireAt(DateTimeInterface $expire_at): void
    {
        $this->expire_at = $expire_at;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }
}
