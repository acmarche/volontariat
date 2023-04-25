<?php

namespace AcMarche\Volontariat\Entity;

use AcMarche\Volontariat\Repository\MessageRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
#[ORM\Table(name: 'message')]
#[ORM\HasLifecycleCallbacks]
class Message implements \Stringable
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    public int $id;

    #[ORM\Column(type: 'string', length: 255)]
    public ?string $sujet;

    #[ORM\Column(type: 'text', nullable: false)]
    #[Assert\NotBlank]
    public ?string $contenu;

    public array $selection_destinataires = [];
    public array $destinataires = [];
    public ?string $from = null;
    public array $froms = [];
    public ?File $file = null;
    public ?string $nom = null;
    public ?string $to = null;
    public ?string $nom_destinataire = null;
    public bool $urlToken = false;

    public function __toString(): string
    {
        return $this->sujet;
    }

}
