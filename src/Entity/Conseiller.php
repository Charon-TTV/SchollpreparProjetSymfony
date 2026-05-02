<?php

namespace App\Entity;

use App\Repository\ConseillerRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Enum\ConseillerType;

#[ORM\Entity(repositoryClass: ConseillerRepository::class)]
class Conseiller extends User
{

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $biographie = null;

    #[ORM\Column(type: 'string', enumType: ConseillerType::class, nullable: true)]
    private ?ConseillerType $type = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $telephone = null;

    public function getBiographie(): ?string
    {
        return $this->biographie;
    }

    public function setBiographie(string $biographie): static
    {
        $this->biographie = $biographie;

        return $this;
    }

    public function getType(): ?ConseillerType
    {
        return $this->type;
    }
    public function setType(?ConseillerType $type): static
    {
        $this->type = $type; return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }
    public function setTelephone(?string $telephone): static
    {
        $this->telephone = $telephone; return $this;
    }
}
