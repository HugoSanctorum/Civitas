<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CompteRenduRepository")
 */
class CompteRendu
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
    private $urlDocument;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Personne", inversedBy="CompteRendus")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Personne;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Probleme", inversedBy="CompteRendus")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Probleme;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Intervenir", inversedBy="CompteRendus")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Intervenir;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrlDocument(): ?string
    {
        return $this->urlDocument;
    }

    public function setUrlDocument(string $urlDocument): self
    {
        $this->urlDocument = $urlDocument;

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

    public function __toString()
    {
        return $this->getUrlDocument();
    }

    public function getIntervenir(): ?Intervenir
    {
        return $this->Intervenir;
    }

    public function setIntervenir(?Intervenir $Intervenir): self
    {
        $this->Intervenir = $Intervenir;

        return $this;
    }


}
