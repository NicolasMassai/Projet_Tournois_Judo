<?php

namespace App\Entity;

use App\Repository\TournoiRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TournoiRepository::class)]
class Tournoi
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_debut = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_fin = null;

    #[ORM\Column(length: 255)]
    private ?string $lieu = null;

    #[ORM\Column]
    private ?int $categorie_age = null;

    #[ORM\Column]
    private ?int $categorie_poids = null;

    /**
     * @var Collection<int, Club>
     */
    #[ORM\OneToMany(targetEntity: Club::class, mappedBy: 'tournoi')]
    private Collection $club;

    #[ORM\OneToOne(mappedBy: 'tournoi', cascade: ['persist', 'remove'])]
    private ?Inscription $inscription = null;

    #[ORM\OneToOne(mappedBy: 'tournoi', cascade: ['persist', 'remove'])]
    private ?HistoriqueCombat $historiqueCombat = null;

    #[ORM\ManyToOne(inversedBy: 'tournoi')]
    private ?Combat $combat = null;

    public function __construct()
    {
        $this->club = new ArrayCollection();
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

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->date_debut;
    }

    public function setDateDebut(\DateTimeInterface $date_debut): static
    {
        $this->date_debut = $date_debut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->date_fin;
    }

    public function setDateFin(\DateTimeInterface $date_fin): static
    {
        $this->date_fin = $date_fin;

        return $this;
    }

    public function getLieu(): ?string
    {
        return $this->lieu;
    }

    public function setLieu(string $lieu): static
    {
        $this->lieu = $lieu;

        return $this;
    }

    public function getCategorieAge(): ?int
    {
        return $this->categorie_age;
    }

    public function setCategorieAge(int $categorie_age): static
    {
        $this->categorie_age = $categorie_age;

        return $this;
    }

    public function getCategoriePoids(): ?int
    {
        return $this->categorie_poids;
    }

    public function setCategoriePoids(int $categorie_poids): static
    {
        $this->categorie_poids = $categorie_poids;

        return $this;
    }

    /**
     * @return Collection<int, Club>
     */
    public function getClub(): Collection
    {
        return $this->club;
    }

    public function addClub(Club $club): static
    {
        if (!$this->club->contains($club)) {
            $this->club->add($club);
            $club->setTournoi($this);
        }

        return $this;
    }

    public function removeClub(Club $club): static
    {
        if ($this->club->removeElement($club)) {
            // set the owning side to null (unless already changed)
            if ($club->getTournoi() === $this) {
                $club->setTournoi(null);
            }
        }

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
            $this->inscription->setTournoi(null);
        }

        // set the owning side of the relation if necessary
        if ($inscription !== null && $inscription->getTournoi() !== $this) {
            $inscription->setTournoi($this);
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
        // unset the owning side of the relation if necessary
        if ($historiqueCombat === null && $this->historiqueCombat !== null) {
            $this->historiqueCombat->setTournoi(null);
        }

        // set the owning side of the relation if necessary
        if ($historiqueCombat !== null && $historiqueCombat->getTournoi() !== $this) {
            $historiqueCombat->setTournoi($this);
        }

        $this->historiqueCombat = $historiqueCombat;

        return $this;
    }

    public function getCombat(): ?Combat
    {
        return $this->combat;
    }

    public function setCombat(?Combat $combat): static
    {
        $this->combat = $combat;

        return $this;
    }
}
