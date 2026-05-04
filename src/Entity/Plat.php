<?php

namespace App\Entity;

use App\Repository\PlatRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlatRepository::class)]
class Plat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    private string $nom;

    #[ORM\Column(type: 'text')]
    private string $descriptionCourte;

    #[ORM\Column]
    private float $prix;

    /**
     * Nom du fichier image (ex: "pizza_123.jpg")
     */
    #[ORM\Column(length: 255)]
    private string $image;

    #[ORM\Column]
    private bool $disponible = true;

    #[ORM\Column]
    private bool $personnalisable = true;

    #[ORM\ManyToOne(inversedBy: 'plats')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Categorie $categorie = null;

    #[ORM\OneToMany(mappedBy: 'plat', targetEntity: Ingredient::class, cascade: ['persist', 'remove'])]
    private Collection $ingredients;

    #[ORM\OneToMany(mappedBy: 'plat', targetEntity: CommandeIngredient::class)]
    private $commandeIngredients;

    public function __construct()
    {
        $this->ingredients = new ArrayCollection();
        $this->disponible = true;
        $this->personnalisable = true;
        $this->commandeIngredients = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    public function getDescriptionCourte(): string
    {
        return $this->descriptionCourte;
    }

    public function setDescriptionCourte(string $descriptionCourte): self
    {
        $this->descriptionCourte = $descriptionCourte;
        return $this;
    }

    public function getPrix(): float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): self
    {
        $this->prix = $prix;
        return $this;
    }

    public function getImage(): string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;
        return $this;
    }

    public function isDisponible(): bool
    {
        return $this->disponible;
    }

    public function setDisponible(bool $disponible): self
    {
        $this->disponible = $disponible;
        return $this;
    }

    public function isPersonnalisable(): bool
    {
        return $this->personnalisable;
    }

    public function setPersonnalisable(bool $personnalisable): self
    {
        $this->personnalisable = $personnalisable;
        return $this;
    }

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): self
    {
        $this->categorie = $categorie;
        return $this;
    }

    public function getIngredients(): Collection
    {
        return $this->ingredients;
    }

    /**
     * @return Collection<int, CommandeIngredient>
     */
    public function getCommandeIngredients(): Collection
    {
        return $this->commandeIngredients;
    }

    public function addCommandeIngredient(CommandeIngredient $commandeIngredient): self
    {
        if (!$this->commandeIngredients->contains($commandeIngredient)) {
            $this->commandeIngredients[] = $commandeIngredient;
            $commandeIngredient->setPlat($this);
        }

        return $this;
    }

    public function removeCommandeIngredient(CommandeIngredient $commandeIngredient): self
    {
        if ($this->commandeIngredients->removeElement($commandeIngredient)) {
            // set the owning side to null (unless already changed)
            if ($commandeIngredient->getPlat() === $this) {
                $commandeIngredient->setPlat(null);
            }
        }

        return $this;
    }
}
