<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ServiceRepository")
 */
class Service
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
     * @ORM\ManyToMany(targetEntity="App\Entity\Commune", inversedBy="Service")
     */
    private $Commune;

    public function __construct()
    {
        $this->Commune = new ArrayCollection();
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
     * @return Collection|Commune[]
     */
    public function getCommune(): Collection
    {
        return $this->Commune;
    }

    public function addCommune(Commune $commune): self
    {
        if (!$this->Commune->contains($commune)) {
            $this->Commune[] = $commune;
        }

        return $this;
    }

    public function removeCommune(Commune $commune): self
    {
        if ($this->Commune->contains($commune)) {
            $this->Commune->removeElement($commune);
        }

        return $this;
    }
    public function __toString()
    {
        return $this->getNom();
    }
}
