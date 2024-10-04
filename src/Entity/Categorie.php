<?php

namespace App\Entity;

use App\Repository\CategorieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategorieRepository::class)]
class Categorie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $categorie_poids = null;

    /**
     * @var Collection<int, Tournoi>
     */
    #[ORM\ManyToMany(targetEntity: Tournoi::class, mappedBy: 'poids')]
    private Collection $tournois;

    /**
     * @var Collection<int, Adherant>
     */
    #[ORM\ManyToMany(targetEntity: Adherant::class, mappedBy: 'poids')]
    private Collection $adherants;

    public function __construct()
    {
        $this->tournois = new ArrayCollection();
        $this->adherants = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategoriePoids(): ?float
    {
        return $this->categorie_poids;
    }

    public function setCategoriePoids(float $categorie_poids): static
    {
        $this->categorie_poids = $categorie_poids;

        return $this;
    }

    /**
     * @return Collection<int, Tournoi>
     */
    public function getTournois(): Collection
    {
        return $this->tournois;
    }

    public function addTournoi(Tournoi $tournoi): static
    {
        if (!$this->tournois->contains($tournoi)) {
            $this->tournois->add($tournoi);
            $tournoi->addPoid($this);
        }

        return $this;
    }

    public function removeTournoi(Tournoi $tournoi): static
    {
        if ($this->tournois->removeElement($tournoi)) {
            $tournoi->removePoid($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Adherant>
     */
    public function getAdherants(): Collection
    {
        return $this->adherants;
    }

    public function addAdherant(Adherant $adherant): static
    {
        if (!$this->adherants->contains($adherant)) {
            $this->adherants->add($adherant);
            $adherant->addPoid($this);
        }

        return $this;
    }

    public function removeAdherant(Adherant $adherant): static
    {
        if ($this->adherants->removeElement($adherant)) {
            $adherant->removePoid($this);
        }

        return $this;
    }
        // Ajout de la méthode __toString pour permettre la conversion en chaîne
        public function __toString(): string
        {
            return (string)$this->categorie_poids; // Retourne la catégorie de poids sous forme de chaîne
        }       
}
