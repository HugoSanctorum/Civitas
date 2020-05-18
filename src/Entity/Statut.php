<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\StatutRepository")
 */
class Statut
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
     * @ORM\OneToMany(targetEntity="App\Entity\HistoriqueStatut", mappedBy="Statut")
     */
    private $HistoriqueStatut;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $couleur;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $icone;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $Description;

    public function __construct()
    {
        $this->HistoriqueStatut = new ArrayCollection();
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
     * @return Collection|HistoriqueStatut[]
     */
    public function getHistoriqueStatut(): Collection
    {
        return $this->HistoriqueStatut;
    }

    public function addHistoriqueStatut(HistoriqueStatut $historiqueStatut): self
    {
        if (!$this->HistoriqueStatut->contains($historiqueStatut)) {
            $this->HistoriqueStatut[] = $historiqueStatut;
            $historiqueStatut->setStatut($this);
        }

        return $this;
    }

    public function removeHistoriqueStatut(HistoriqueStatut $historiqueStatut): self
    {
        if ($this->HistoriqueStatut->contains($historiqueStatut)) {
            $this->HistoriqueStatut->removeElement($historiqueStatut);
            // set the owning side to null (unless already changed)
            if ($historiqueStatut->getStatut() === $this) {
                $historiqueStatut->setStatut(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->getNom();
    }

    public function getCouleur(): ?string
    {
        return $this->couleur;
    }

    public function setCouleur(?string $couleur): self
    {
        $this->couleur = $couleur;

        return $this;
    }

    public function getIcone(): ?string
    {
        return $this->icone;
    }

    public function setIcone(?string $icone): self
    {
        $this->icone = $icone;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->Description;
    }

    public function setDescription(?string $Description): self
    {
        $this->Description = $Description;

        return $this;
    }

}
