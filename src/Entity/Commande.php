<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'commandes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $client = null;

    #[ORM\Column]
    private ?float $total = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\OneToMany(mappedBy: 'commande', targetEntity: CommandeIngredient::class)]
    private $commandeIngredients;

    public function __construct()
    {
        $this->commandeIngredients = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClient(): ?User
    {
        return $this->client;
    }

    public function setClient(?User $client): self
    {
        $this->client = $client;
        return $this;
    }

    public function getTotal(): ?float
    {
        return $this->total;
    }

    public function setTotal(float $total): self
    {
        $this->total = $total;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
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
            $commandeIngredient->setCommande($this);
        }

        return $this;
    }

    public function removeCommandeIngredient(CommandeIngredient $commandeIngredient): self
    {
        if ($this->commandeIngredients->removeElement($commandeIngredient)) {
            // set the owning side to null (unless already changed)
            if ($commandeIngredient->getCommande() === $this) {
                $commandeIngredient->setCommande(null);
            }
        }

        return $this;
    }
}
