<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Repository\PlatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class IngredientController extends AbstractController
{
    #[Route('/ingredient/add', name: 'ingredient_add', methods: ['POST'])]
    public function add(
        Request $request,
        PlatRepository $platRepo,
        EntityManagerInterface $em
    ): JsonResponse {

        $platId = $request->request->get('platId');
        $plat = $platRepo->find($platId);

        if (!$plat) {
            return new JsonResponse(['error' => 'Plat introuvable'], 404);
        }

        $ingredient = new Ingredient();
        $ingredient->setNom($request->request->get('nom'));
        $ingredient->setEssentiel($request->request->get('essentiel') === 'true');
        $ingredient->setPlat($plat);

        $em->persist($ingredient);
        $em->flush();

        return new JsonResponse(['success' => true]);
    }

    #[Route('/ingredient/delete/{id}', name: 'ingredient_delete', methods: ['GET'])]
    public function delete(
        Ingredient $ingredient,
        EntityManagerInterface $em
    ): JsonResponse {

        $em->remove($ingredient);
        $em->flush();

        return new JsonResponse(['success' => true]);
    }

    #[Route('/ingredient/edit/{id}', name: 'ingredient_edit', methods: ['POST'])]
    public function edit(
        Ingredient $ingredient,
        Request $request,
        EntityManagerInterface $em
    ): JsonResponse {

        $ingredient->setNom($request->request->get('nom'));
        $ingredient->setEssentiel($request->request->get('essentiel') === 'true');

        $em->flush();

        return new JsonResponse(['success' => true]);
    }
}
