<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\IntervenirRepository")
 */
class Intervenir
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Personne", inversedBy="Intervenir")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Personne;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Probleme", inversedBy="Intervenirs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Probleme;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\TypeIntervention", inversedBy="Interventions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $TypeIntervention;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CompteRendu", mappedBy="Intervenir")
     */
    private $CompteRendus;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    public function __construct()
    {
        $this->CompteRendus = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getPersonne(): ?Personne
    {
        return $this->Personne;
    }

    public function setPersonne(?Personne $Personne): self
    {
        $this->Personne = $Personne;

        return $this;
    }

    public function getProbleme(): ?Probleme
    {
        return $this->Probleme;
    }

    public function setProbleme(?Probleme $Probleme): self
    {
        $this->Probleme = $Probleme;

        return $this;
    }
    public function __toString():string
    {
        return $this->getPersonne();
    }

    public function getTypeIntervention(): ?TypeIntervention
    {
        return $this->TypeIntervention;
    }

    public function setTypeIntervention(?TypeIntervention $TypeIntervention): self
    {
        $this->TypeIntervention = $TypeIntervention;

        return $this;
    }

    /**
     * @return Collection|CompteRendu[]
     */
    public function getCompteRendus(): Collection
    {
        return $this->CompteRendus;
    }

    public function addCompteRendus(CompteRendu $compteRendus): self
    {
        if (!$this->CompteRendus->contains($compteRendus)) {
            $this->CompteRendus[] = $compteRendus;
            $compteRendus->setIntervenir($this);
        }

        return $this;
    }

    public function removeCompteRendus(CompteRendu $compteRendus): self
    {
        if ($this->CompteRendus->contains($compteRendus)) {
            $this->CompteRendus->removeElement($compteRendus);
            // set the owning side to null (unless already changed)
            if ($compteRendus->getIntervenir() === $this) {
                $compteRendus->setIntervenir(null);
            }
        }

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }
}
