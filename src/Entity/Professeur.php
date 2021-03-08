<?php

namespace App\Entity;

use JsonSerializable;

use App\Repository\ProfesseurRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=ProfesseurRepository::class)
 * @UniqueEntity("email")
 */
class Professeur implements JsonSerializable {
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;
    
    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(
     *      message = "Veuillez renseigner le nom."
     * )
     */
    private $nom;
    
    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(
     *      message = "Veuillez renseigner le prénom."
     * )
     */
    private $prenom;
    
    /**
     * @ORM\Column(type="string", unique=true, length=255)
     * @Assert\NotBlank(
     *      message = "Veuillez renseigner l'email."
     * )
     * @Assert\Email(
     *      message = "Le format de l'email est invalide."
     * )
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

    /**
     * @ORM\OneToMany(targetEntity=Cours::class, mappedBy="professeur")
     */
    private $cours;

    public function __construct() {
        $this->avis = new ArrayCollection();
        $this->matieres = new ArrayCollection();
        $this->cours = new ArrayCollection();
    }

    public function __toString() {
        return $this->prenom . ' ' . $this->nom;
    }

    public function jsonSerialize() {
        return [
            'id' => $this->id,
            'nom' => $this->nom,
            'prenom' => $this->prenom,
            'email' => $this->email,
            'matieres' => $this->matieres->toArray()
        ];
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

    /**
     * @return Collection|Avis[]
     */
    public function getAvis(): Collection {
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

    /**
     * @return Collection|Cours[]
     */
    public function getCours(): Collection
    {
        return $this->cours;
    }

    public function addCour(Cours $cour): self
    {
        if (!$this->cours->contains($cour)) {
            $this->cours[] = $cour;
            $cour->setProfesseur($this);
        }

        return $this;
    }

    public function removeCour(Cours $cour): self
    {
        if ($this->cours->removeElement($cour)) {
            // set the owning side to null (unless already changed)
            if ($cour->getProfesseur() === $this) {
                $cour->setProfesseur(null);
            }
        }

        return $this;
    }
}
