<?php

namespace AcMarche\Volontariat\Entity;

use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

trait PasswordAuthenticableTrait
{
    #[ORM\Column(type: Types::STRING, nullable: true)]
    public ?string $password = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    public ?string $salt = null;

    public ?string $plainPassword = null;

    #[ORM\Column(type: Types::STRING, length: 50, nullable: true)]
    public ?string $tokenValue = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    public ?DateTimeInterface $tokenExpireAt = null;

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getSalt(): ?string
    {
        return (string) $this->salt;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    public function getPasswordHasherName(): ?string
    {
        return null;
    }
}
