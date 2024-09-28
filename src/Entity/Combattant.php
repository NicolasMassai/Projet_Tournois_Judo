<?php

namespace App\Entity;

use App\Repository\CombattantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CombattantRepository::class)]
class Combattant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $prenom = null;

    #[ORM\Column]
    private ?int $age = null;

    #[ORM\Column]
    private ?float $poids = null;

    #[ORM\Column]
    private ?int $classement = null;

    #[ORM\OneToOne(mappedBy: 'combattant', cascade: ['persist', 'remove'])]
    private ?Inscription $inscription = null;

    #[ORM\ManyToOne(inversedBy: 'combattant')]
    private ?HistoriqueCombat $historiqueCombat = null;

    #[ORM\OneToOne(mappedBy: 'combattant', cascade: ['persist', 'remove'])]
    private ?Adherant $adherant = null;

    #[ORM\ManyToOne(inversedBy: 'combattant')]
    private ?Club $club = null;

    public function __construct()
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(int $age): static
    {
        $this->age = $age;

        return $this;
    }

    public function getPoids(): ?float
    {
        return $this->poids;
    }

    public function setPoids(float $poids): static
    {
        $this->poids = $poids;

        return $this;
    }

    public function getClassement(): ?int
    {
        return $this->classement;
    }

    public function setClassement(int $classement): static
    {
        $this->classement = $classement;

        return $this;
    }

    public function getInscription(): ?Inscription
    {
        return $this->inscription;
    }

    public function setInscription(?Inscription $inscription): static
    {
        // unset the owning side of the relation if necessary
        if ($inscription === null && $this->inscription !== null) {
            $this->inscription->setCombattant(null);
        }

        // set the owning side of the relation if necessary
        if ($inscription !== null && $inscription->getCombattant() !== $this) {
            $inscription->setCombattant($this);
        }

        $this->inscription = $inscription;

        return $this;
    }

    public function getHistoriqueCombat(): ?HistoriqueCombat
    {
        return $this->historiqueCombat;
    }

    public function setHistoriqueCombat(?HistoriqueCombat $historiqueCombat): static
    {
        $this->historiqueCombat = $historiqueCombat;

        return $this;
    }

    public function getAdherant(): ?Adherant
    {
        return $this->adherant;
    }

    public function setAdherant(?Adherant $adherant): static
    {
        // unset the owning side of the relation if necessary
        if ($adherant === null && $this->adherant !== null) {
            $this->adherant->setCombattant(null);
        }

        // set the owning side of the relation if necessary
        if ($adherant !== null && $adherant->getCombattant() !== $this) {
            $adherant->setCombattant($this);
        }

        $this->adherant = $adherant;

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
