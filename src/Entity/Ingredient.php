<?php

namespace App\Entity;

use App\Repository\IngredientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: IngredientRepository::class)]
class Ingredient
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $nom = null;

    #[ORM\Column]
    private bool $essentiel = false;

    #[ORM\ManyToOne(inversedBy: 'ingredients')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Plat $plat = null;

    #[ORM\OneToMany(mappedBy: 'ingredient', targetEntity: CommandeIngredient::class)]
    private $commandeIngredients;

    public function __construct()
    {
        $this->commandeIngredients = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getNom(): ?string { return $this->nom; }
    public function setNom(string $nom): self { $this->nom = $nom; return $this; }

    public function isEssentiel(): bool { return $this->essentiel; }
    public function setEssentiel(bool $essentiel): self { $this->essentiel = $essentiel; return $this; }

    public function getPlat(): ?Plat { return $this->plat; }
    public function setPlat(?Plat $plat): self { $this->plat = $plat; return $this; }

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
            $commandeIngredient->setIngredient($this);
        }

        return $this;
    }

    public function removeCommandeIngredient(CommandeIngredient $commandeIngredient): self
    {
        if ($this->commandeIngredients->removeElement($commandeIngredient)) {
            // set the owning side to null (unless already changed)
            if ($commandeIngredient->getIngredient() === $this) {
                $commandeIngredient->setIngredient(null);
            }
        }

        return $this;
    }
}
