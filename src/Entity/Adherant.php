<?php

namespace App\Entity;

use App\Repository\AdherantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AdherantRepository::class)]
class Adherant extends User
{
    #[ORM\OneToOne(inversedBy: 'adherant', cascade: ['persist', 'remove'])]
    private ?Combattant $combattant = null;

    #[ORM\ManyToOne(inversedBy: 'adherant')]
    private ?Club $club = null;

    public function __construct()
    {
    }

    public function getCombattant(): ?Combattant
    {
        return $this->combattant;
    }

    public function setCombattant(?Combattant $combattant): static
    {
        $this->combattant = $combattant;

        return $this;
    }

    public function getClub(): ?Club
    {
        return $this->club;
    }

    public function setClub(?Club $club): static
    {
        $this->club = $club;

        return $this;
    }
}
