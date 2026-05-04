<?php

namespace App\Controller;

use App\Entity\Plat;
use App\Repository\PlatRepository;
use App\Repository\CategorieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(PlatRepository $platRepo, CategorieRepository $catRepo): Response
    {
        return $this->render('home/index.html.twig', [
            'categories' => $catRepo->findAll(),
            'plats' => $platRepo->findBy(['disponible' => true]),
            'activeSlug' => null
        ]);
    }

    #[Route('/categorie/{slug}', name: 'home_categorie')]
    public function categorie(
        string $slug,
        CategorieRepository $catRepo,
        PlatRepository $platRepo
    ): Response {

        $categorie = $catRepo->findOneBy(['slug' => $slug]);
        if (!$categorie) {
            throw $this->createNotFoundException("Catégorie introuvable");
        }

        return $this->render('home/index.html.twig', [
            'categories' => $catRepo->findAll(),
            'plats' => $platRepo->findBy([
                'categorie' => $categorie,
                'disponible' => true
            ]),
            'activeSlug' => $slug
        ]);
    }

    /* -----------------------------------------
       🔥 ROUTE AJAX : INGREDIENTS D’UN PLAT
    ----------------------------------------- */
    #[Route('/plat/{id}/ingredients', name: 'plat_ingredients')]
    #[ParamConverter('plat', class: Plat::class)]
    public function ingredients(Plat $plat): JsonResponse
    {
        $ingredients = [];

        foreach ($plat->getIngredients() as $ing) {
            $ingredients[] = [
                'id' => $ing->getId(),
                'nom' => $ing->getNom(),
                'essentiel' => $ing->isEssentiel(),
            ];
        }

        return new JsonResponse([
            'plat' => $plat->getNom(),
            'ingredients' => $ingredients
        ]);
    }
}
