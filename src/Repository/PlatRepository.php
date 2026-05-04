<?php

namespace App\Repository;

use App\Entity\Plat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PlatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Plat::class);
    }

    /**
     * Retourne uniquement les plats disponibles
     */
    public function findDisponibles(): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.disponible = :val')
            ->setParameter('val', true)
            ->orderBy('p.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne les plats d'une catégorie via son slug
     */
    public function findByCategorieSlug(string $slug): array
    {
        return $this->createQueryBuilder('p')
            ->join('p.categorie', 'c')
            ->andWhere('c.slug = :slug')
            ->setParameter('slug', $slug)
            ->orderBy('p.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
