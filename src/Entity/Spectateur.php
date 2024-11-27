<?php

namespace App\Entity;

use App\Repository\SpectateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SpectateurRepository::class)]
class Spectateur extends User
{
 
    #[ORM\Column]
    private ?int $note = null;

    /**
     * @var Collection<int, Note>
     */
    #[ORM\OneToMany(targetEntity: Note::class, mappedBy: 'spectateur')]
    private Collection $notes;

    public function __construct()
    {
        parent::__construct();
        $this->notes = new ArrayCollection();
    }


    public function getNote(): ?int
    {
        return $this->note;
    }

    public function setNote(int $note): static
    {
        $this->note = $note;

        return $this;
    }

    /**
     * @return Collection<int, Note>
     */
    public function getNotes(): Collection
    {
        return $this->notes;
    }

    public function addNote(Note $note): static
    {
        if (!$this->notes->contains($note)) {
            $this->notes->add($note);
            $note->setSpectateur($this);
        }

        return $this;
    }

    public function removeNote(Note $note): static
    {
        if ($this->notes->removeElement($note)) {
            // set the owning side to null (unless already changed)
            if ($note->getSpectateur() === $this) {
                $note->setSpectateur(null);
            }
        }

        return $this;
    }
}
