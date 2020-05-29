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
     * @ORM\Column(type="array")
     */
    private $centre;

    /**
     * @ORM\Column(type="array")
     */
    private $codesPostaux;

    /**
     * @ORM\Column(type="integer")
     */
    private $codeRegion;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Service", mappedBy="Commune")
     */
    private $Service;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Probleme", mappedBy="Commune")
     */
    private $Problemes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Personne", mappedBy="Commune")
     */
    private $Personnes;

    /**
     * @ORM\Column(type="integer")
     */
    private $codeDepartement;

    /**
     * @ORM\Column(type="array")
     */
    private $contour = [];

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $codeInsee;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $imageBackground;

    public function __construct()
    {
        $this->Service = new ArrayCollection();
        $this->Problemes = new ArrayCollection();
        $this->Personnes = new ArrayCollection();
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

    public function getCentre(): ?array
    {
        return $this->centre;
    }

    public function setCentre(array $centre): self
    {
        $this->centre = $centre;

        return $this;
    }

    public function getCodesPostaux(): ?array
    {
        return $this->codesPostaux;
    }

    public function setCodesPostaux(array $codesPostaux): self
    {
        $this->codesPostaux = $codesPostaux;

        return $this;
    }

    public function getCodeRegion(): ?int
    {
        return $this->codeRegion;
    }

    public function setCodeRegion(int $codeRegion): self
    {
        $this->codeRegion = $codeRegion;

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

    /**
     * @return Collection|Personne[]
     */
    public function getPersonnes(): Collection
    {
        return $this->Personnes;
    }

    public function addPersonne(Personne $personne): self
    {
        if (!$this->Personnes->contains($personne)) {
            $this->Personnes[] = $personne;
            $personne->setCommune($this);
        }

        return $this;
    }

    public function removePersonne(Personne $personne): self
    {
        if ($this->Personnes->contains($personne)) {
            $this->Personnes->removeElement($personne);
            // set the owning side to null (unless already changed)
            if ($personne->getCommune() === $this) {
                $personne->setCommune(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->getNom();
    }

    public function getCodeDepartement(): ?int
    {
        return $this->codeDepartement;
    }

    public function setCodeDepartement(int $codeDepartement): self
    {
        $this->codeDepartement = $codeDepartement;

        return $this;
    }

    public function getContour(): ?array
    {
        return $this->contour;
    }

    public function setContour(array $contour): self
    {
        $this->contour = $contour;

        return $this;
    }

    public function getCodeInsee(): ?string
    {
        return $this->codeInsee;
    }

    public function setCodeInsee(string $codeInsee): self
    {
        $this->codeInsee = $codeInsee;

        return $this;
    }

    public function getImageBackground(): ?string
    {
        return $this->imageBackground;
    }

    public function setImageBackground(?string $imageBackground): self
    {
        $this->imageBackground = $imageBackground;

        return $this;
    }
}