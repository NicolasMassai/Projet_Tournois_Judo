<?php

namespace App\Entity;

use App\Repository\ArbitreRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ArbitreRepository::class)]
class Arbitre extends User
{

    #[ORM\Column(length: 255)]
    private ?string $qualification = null;

    #[ORM\Column]
    private ?bool $disponibilite = null;


    public function getQualification(): ?string
    {
        return $this->qualification;
    }

    public function setQualification(string $qualification): static
    {
        $this->qualification = $qualification;

        return $this;
    }

    public function isDisponibilite(): ?bool
    {
        return $this->disponibilite;
    }

    public function setDisponibilite(bool $disponibilite): static
    {
        $this->disponibilite = $disponibilite;

        return $this;
    }
  
}
