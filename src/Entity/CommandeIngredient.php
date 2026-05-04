<?php

namespace App\Entity;

use App\Repository\CommandeIngredientRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommandeIngredientRepository::class)]
class CommandeIngredient
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Commande::class, inversedBy: 'commandeIngredients')]
    private $commande;

    #[ORM\ManyToOne(targetEntity: Plat::class, inversedBy: 'commandeIngredients')]
    private $plat;

    #[ORM\ManyToOne(targetEntity: Ingredient::class, inversedBy: 'commandeIngredients')]
    private $ingredient;

    #[ORM\Column(type: 'string', length: 255)]
    private $action;

    #[ORM\ManyToOne(targetEntity: Ingredient::class, inversedBy: 'commandeIngredients')]
    private $replacedBy;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $quantity;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCommande(): ?Commande
    {
        return $this->commande;
    }

    public function setCommande(?Commande $commande): self
    {
        $this->commande = $commande;

        return $this;
    }

    public function getPlat(): ?Plat
    {
        return $this->plat;
    }

    public function setPlat(?Plat $plat): self
    {
        $this->plat = $plat;

        return $this;
    }

    public function getIngredient(): ?Ingredient
    {
        return $this->ingredient;
    }

    public function setIngredient(?Ingredient $ingredient): self
    {
        $this->ingredient = $ingredient;

        return $this;
    }

    public function getAction(): ?string
    {
        return $this->action;
    }

    public function setAction(string $action): self
    {
        $this->action = $action;

        return $this;
    }

    public function getReplacedBy(): ?Ingredient
    {
        return $this->replacedBy;
    }

    public function setReplacedBy(?Ingredient $replacedBy): self
    {
        $this->replacedBy = $replacedBy;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(?int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }
}
