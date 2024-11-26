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
     * @var Collection<int, Adherant>
     */
    #[ORM\OneToMany(targetEntity: Adherant::class, mappedBy: 'categorie')]
    private Collection $adherant;

    /**
     * @var Collection<int, CategorieTournoi>
     */
    #[ORM\OneToMany(targetEntity: CategorieTournoi::class, mappedBy: 'categorie')]
    private Collection $categorieTournois;
  

    public function __construct()
    {
        $this->adherant = new ArrayCollection();
        $this->categorieTournois = new ArrayCollection();
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

  
        // Ajout de la méthode __toString pour permettre la conversion en chaîne
        public function __toString(): string
        {
            return (string)$this->categorie_poids; // Retourne la catégorie de poids sous forme de chaîne
        }


        /**
         * @return Collection<int, Adherant>
         */
        public function getAdherant(): Collection
        {
            return $this->adherant;
        }

        public function addAdherant(Adherant $adherant): static
        {
            if (!$this->adherant->contains($adherant)) {
                $this->adherant->add($adherant);
                $adherant->setCategorie($this);
            }

            return $this;
        }

        public function removeAdherant(Adherant $adherant): static
        {
            if ($this->adherant->removeElement($adherant)) {
                // set the owning side to null (unless already changed)
                if ($adherant->getCategorie() === $this) {
                    $adherant->setCategorie(null);
                }
            }

            return $this;
        }

        /**
         * @return Collection<int, CategorieTournoi>
         */
        public function getCategorieTournois(): Collection
        {
            return $this->categorieTournois;
        }

        public function addCategorieTournoi(CategorieTournoi $categorieTournoi): static
        {
            if (!$this->categorieTournois->contains($categorieTournoi)) {
                $this->categorieTournois->add($categorieTournoi);
                $categorieTournoi->setCategorie($this);
            }

            return $this;
        }

        public function removeCategorieTournoi(CategorieTournoi $categorieTournoi): static
        {
            if ($this->categorieTournois->removeElement($categorieTournoi)) {
                // set the owning side to null (unless already changed)
                if ($categorieTournoi->getCategorie() === $this) {
                    $categorieTournoi->setCategorie(null);
                }
            }

            return $this;
        }
      
}
