<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CategorieRepository")
 */
class Categorie
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
     * @ORM\OneToMany(targetEntity="App\Entity\Probleme", mappedBy="Categorie")
     */
    private $Problemes;

    public function __construct()
    {
        $this->Problemes = new ArrayCollection();
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
     * @return Collection|Probleme[]
     */
    public function getProblemes(): Collection
    {
        return $this->Problemes;
    }

    public function addProbleme(Probleme $probleme): self
    {
        if (!$this->Problemes->contains($probleme)) {
            $this->Problemes[] = $probleme;
            $probleme->setCategorie($this);
        }

        return $this;
    }

    public function removeProbleme(Probleme $probleme): self
    {
        if ($this->Problemes->contains($probleme)) {
            $this->Problemes->removeElement($probleme);
            // set the owning side to null (unless already changed)
            if ($probleme->getCategorie() === $this) {
                $probleme->setCategorie(null);
            }
        }

        return $this;
    }
}
