<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\TournoiRepository;

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

    #[ORM\OneToOne(mappedBy: 'tournoi', cascade: ['persist', 'remove'])]
    private ?Inscription $inscription = null;

    #[ORM\OneToOne(mappedBy: 'tournoi', cascade: ['persist', 'remove'])]
    private ?HistoriqueCombat $historiqueCombat = null;



    /**
     * @var Collection<int, Categorie>
     */
    #[ORM\ManyToMany(targetEntity: Categorie::class, inversedBy: 'tournois')]
    private Collection $poids;

    /**
     * @var Collection<int, Club>
     */
    #[ORM\ManyToMany(targetEntity: Club::class, inversedBy: 'tournois')]
    private Collection $clubs;

    /**
     * @var Collection<int, Adherant>
     */
    #[ORM\ManyToMany(targetEntity: Adherant::class, inversedBy: 'tournois')]
    private Collection $combattant;

    /**
     * @var Collection<int, Combat>
     */
    #[ORM\OneToMany(targetEntity: Combat::class, mappedBy: 'tournoi')]
    private Collection $combats;

    #[ORM\Column(nullable: true)]
    private ?bool $combatGenerer = false;

    /**
     * @var Collection<int, Groupe>
     */
    #[ORM\OneToMany(targetEntity: Groupe::class, mappedBy: 'tournoi')]
    private Collection $groupes;

    public function __construct()
    {
        $this->poids = new ArrayCollection();
        $this->clubs = new ArrayCollection();
        $this->combattant = new ArrayCollection();
        $this->combats = new ArrayCollection();
        $this->groupes = new ArrayCollection();
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

    /**
     * @return Collection<int, Categorie>
     */
    public function getPoids(): Collection
    {
        return $this->poids;
    }

    public function addPoid(Categorie $poid): static
    {
        if (!$this->poids->contains($poid)) {
            $this->poids->add($poid);
        }

        return $this;
    }

    public function removePoid(Categorie $poid): static
    {
        $this->poids->removeElement($poid);

        return $this;
    }

    /**
     * @return Collection<int, Club>
     */
    public function getClubs(): Collection
    {
        return $this->clubs;
    }

    public function addClub(Club $club): static
    {
        if (!$this->clubs->contains($club)) {
            $this->clubs->add($club);
        }

        return $this;
    }

    public function removeClub(Club $club): static
    {
        $this->clubs->removeElement($club);

        return $this;
    }

    /**
     * @return Collection<int, Adherant>
     */
    public function getCombattant(): Collection
    {
        return $this->combattant;
    }

    public function addCombattant(Adherant $combattant): static
    {
        if (!$this->combattant->contains($combattant)) {
            $this->combattant->add($combattant);
        }

        return $this;
    }

    public function removeCombattant(Adherant $combattant): static
    {
        $this->combattant->removeElement($combattant);

        return $this;
    }

    /**
     * @return Collection<int, Combat>
     */
    public function getCombats(): Collection
    {
        return $this->combats;
    }

    public function addCombat(Combat $combat): static
    {
        if (!$this->combats->contains($combat)) {
            $this->combats->add($combat);
            $combat->setTournoi($this);
        }

        return $this;
    }

    public function removeCombat(Combat $combat): static
    {
        if ($this->combats->removeElement($combat)) {
            // set the owning side to null (unless already changed)
            if ($combat->getTournoi() === $this) {
                $combat->setTournoi(null);
            }
        }

        return $this;
    }

    public function isCombatGenerer(): ?bool
    {
        return $this->combatGenerer;
    }

    public function setCombatGenerer(?bool $combatGenerer): static
    {
        $this->combatGenerer = $combatGenerer;

        return $this;
    }

    /**
     * @return Collection<int, Groupe>
     */
    public function getGroupes(): Collection
    {
        return $this->groupes;
    }

    public function addGroupe(Groupe $groupe): static
    {
        if (!$this->groupes->contains($groupe)) {
            $this->groupes->add($groupe);
            $groupe->setTournoi($this);
        }

        return $this;
    }

    public function removeGroupe(Groupe $groupe): static
    {
        if ($this->groupes->removeElement($groupe)) {
            // set the owning side to null (unless already changed)
            if ($groupe->getTournoi() === $this) {
                $groupe->setTournoi(null);
            }
        }

        return $this;
    }

}
