<?php

namespace App\Entity;

use App\Repository\LivreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LivreRepository::class)]
class Livre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $Titre = null;

    #[ORM\Column(length: 75)]
    private ?string $Auteur = null;

    #[ORM\Column]
    private ?float $Prix = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $DatePubli = null;

    #[ORM\Column(length: 50)]
    private ?string $Format = null;

    #[ORM\Column(length: 50)]
    private ?string $Editeur = null;

    #[ORM\Column(length: 50)]
    private ?string $Langue = null;

    #[ORM\Column(length: 100)]
    private ?string $Couverture = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $Resume = null;


    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'favories')]
    private Collection $users;

    #[ORM\Column(length: 100)]
    private ?string $genre = null;

    #[ORM\Column(length: 15)]
    private ?string $type = null;

    #[ORM\OneToMany(mappedBy: 'livre', targetEntity: Ajouter::class)]
    private Collection $ajouters;

    public function __construct()
    {
        $this->livres = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->ajouters = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->Titre;
    }

    public function setTitre(string $Titre): self
    {
        $this->Titre = $Titre;

        return $this;
    }

    public function getAuteur(): ?string
    {
        return $this->Auteur;
    }

    public function setAuteur(string $Auteur): self
    {
        $this->Auteur = $Auteur;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->Prix;
    }

    public function setPrix(float $Prix): self
    {
        $this->Prix = $Prix;

        return $this;
    }

    public function getDatePubli(): ?\DateTimeInterface
    {
        return $this->DatePubli;
    }

    public function setDatePubli(\DateTimeInterface $DatePubli): self
    {
        $this->DatePubli = $DatePubli;

        return $this;
    }

    public function getFormat(): ?string
    {
        return $this->Format;
    }

    public function setFormat(string $Format): self
    {
        $this->Format = $Format;

        return $this;
    }

    public function getEditeur(): ?string
    {
        return $this->Editeur;
    }

    public function setEditeur(string $Editeur): self
    {
        $this->Editeur = $Editeur;

        return $this;
    }

    public function getLangue(): ?string
    {
        return $this->Langue;
    }

    public function setLangue(string $Langue): self
    {
        $this->Langue = $Langue;

        return $this;
    }

    public function getCouverture(): ?string
    {
        return $this->Couverture;
    }

    public function setCouverture(string $Couverture): self
    {
        $this->Couverture = $Couverture;

        return $this;
    }

    public function getResume(): ?string
    {
        return $this->Resume;
    }

    public function setResume(string $Resume): self
    {
        $this->Resume = $Resume;

        return $this;
    }


    /**
     * @return Collection<int, self>
     */
    public function getLivres(): Collection
    {
        return $this->livres;
    }

    public function addLivre(self $livre): self
    {
        if (!$this->livres->contains($livre)) {
            $this->livres->add($livre);
        }

        return $this;
    }

    public function removeLivre(self $livre): self
    {
        if ($this->livres->removeElement($livre)) {
            $livre->removeGenre($this);
        }

        return $this;
    }


    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->addFavory($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            $user->removeFavory($this);
        }

        return $this;
    }

    public function getGenre(): ?string
    {
        return $this->genre;
    }

    public function setGenre(string $genre): self
    {
        $this->genre = $genre;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Collection<int, Ajouter>
     */
    public function getAjouters(): Collection
    {
        return $this->ajouters;
    }

    public function addAjouter(Ajouter $ajouter): self
    {
        if (!$this->ajouters->contains($ajouter)) {
            $this->ajouters->add($ajouter);
            $ajouter->setLivre($this);
        }

        return $this;
    }

    public function removeAjouter(Ajouter $ajouter): self
    {
        if ($this->ajouters->removeElement($ajouter)) {
            // set the owning side to null (unless already changed)
            if ($ajouter->getLivre() === $this) {
                $ajouter->setLivre(null);
            }
        }

        return $this;
    }
}
