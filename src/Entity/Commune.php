<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CommuneRepository")
 */
class Commune
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
     * @ORM\Column(type="string", length=5)
     */
    private $code_postal;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $region;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Service", mappedBy="Commune")
     */
    private $Service;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Probleme", mappedBy="Commune")
     */
    private $Problemes;

    public function __construct()
    {
        $this->Service = new ArrayCollection();
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

    public function getCodePostal(): ?string
    {
        return $this->code_postal;
    }

    public function setCodePostal(string $code_postal): self
    {
        $this->code_postal = $code_postal;

        return $this;
    }

    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function setRegion(string $region): self
    {
        $this->region = $region;

        return $this;
    }

    /**
     * @return Collection|Service[]
     */
    public function getService(): Collection
    {
        return $this->Service;
    }

    public function addService(Service $service): self
    {
        if (!$this->Service->contains($service)) {
            $this->Service[] = $service;
            $service->addCommune($this);
        }

        return $this;
    }

    public function removeService(Service $service): self
    {
        if ($this->Service->contains($service)) {
            $this->Service->removeElement($service);
            $service->removeCommune($this);
        }

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
            $probleme->setCommune($this);
        }

        return $this;
    }

    public function removeProbleme(Probleme $probleme): self
    {
        if ($this->Problemes->contains($probleme)) {
            $this->Problemes->removeElement($probleme);
            // set the owning side to null (unless already changed)
            if ($probleme->getCommune() === $this) {
                $probleme->setCommune(null);
            }
        }

        return $this;
    }
}
