<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TypeInterventionRepository")
 */
class TypeIntervention
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Intervenir", mappedBy="TypeIntervention")
     */
    private $Interventions;

    public function __construct()
    {
        $this->Interventions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * @return Collection|Intervenir[]
     */
    public function getInterventions(): Collection
    {
        return $this->Interventions;
    }

    public function addIntervention(Intervenir $intervention): self
    {
        if (!$this->Interventions->contains($intervention)) {
            $this->Interventions[] = $intervention;
            $intervention->setTypeIntervention($this);
        }

        return $this;
    }

    public function removeIntervention(Intervenir $intervention): self
    {
        if ($this->Interventions->contains($intervention)) {
            $this->Interventions->removeElement($intervention);
            // set the owning side to null (unless already changed)
            if ($intervention->getTypeIntervention() === $this) {
                $intervention->setTypeIntervention(null);
            }
        }

        return $this;
    }

    public function __toString(): ?string
    {
        return $this->nom;
    }
}
