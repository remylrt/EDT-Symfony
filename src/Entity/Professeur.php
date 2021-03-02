<?php

namespace App\Entity;

use App\Repository\ProfesseurRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @ORM\Entity(repositoryClass=ProfesseurRepository::class)
 */
class Professeur {
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;
    
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;
    
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $prenom;
    
    /**
     * @ORM\Column(type="string", unique=true, length=255)
     */
    private $email;

    /**
     * @ORM\OneToMany(targetEntity=Avis::class, mappedBy="professeur")
     */
    private $avis;

    /**
     * @ORM\ManyToMany(targetEntity=Matiere::class, mappedBy="professeurs")
     */
    private $matieres;

    public function __construct() {
        $this->avis = new ArrayCollection();
        $this->matieres = new ArrayCollection();
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getNom(): ?string {
        return $this->nom;
    }

    public function setNom(string $nom): self {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self {
        $this->prenom = $prenom;

        return $this;
    }

    public function getEmail(): ?string {
        return $this->email;
    }

    public function setEmail(string $email): self {
        $this->email = $email;

        return $this;
    }

    public function getAvis(): ArrayCollection {
        return $this->avis;
    }

    public function addAvis(Avis $avis): self {
        if (!$this->avis->contains($avis)) {
            $this->avis[] = $avis;
            $avis->setProfesseur($this);
        }

        return $this;
    }

    public function removeAvis(Avis $avis): self {
        if ($this->avis->removeElement($avis)) {
            if ($avis->getProfesseur() === $this) {
                $avis->setProfesseur(null);
            }
        }
    }

    /**
     * @return Collection|Matiere[]
     */
    public function getMatieres(): Collection
    {
        return $this->matieres;
    }

    public function addMatiere(Matiere $matiere): self
    {
        if (!$this->matieres->contains($matiere)) {
            $this->matieres[] = $matiere;
            $matiere->addProfesseur($this);
        }

        return $this;
    }

    public function removeMatiere(Matiere $matiere): self
    {
        if ($this->matieres->removeElement($matiere)) {
            $matiere->removeProfesseur($this);
        }

        return $this;
    }
}
