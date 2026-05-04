<?php

namespace App\Controller;

use App\Entity\Plat;
use App\Repository\PlatRepository;
use App\Repository\IngredientRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PanierController extends AbstractController
{
    /* -----------------------------------------
       PAGE PANIER
    ----------------------------------------- */
    #[Route('/panier', name: 'panier')]
    public function index(
        Request $request,
        PlatRepository $platRepo,
        IngredientRepository $ingredientRepo
    ): Response {
        $session = $request->getSession();
        $panier = $session->get('panier', []);

        $items = [];
        $total = 0;

        foreach ($panier as $item) {

            $plat = $platRepo->find($item['id']);
            if (!$plat) continue;

            $lineTotal = $plat->getPrix() * $item['quantity'];

            $items[] = [
                'plat' => $plat,
                'quantity' => $item['quantity'],
                'removed' => $item['removed'] ?? [],
                'replaced' => $item['replaced'] ?? [],
                'total' => $lineTotal
            ];

            $total += $lineTotal;
        }

        // Tableau des ingrédients (id => nom)
        $ingredients = [];
        foreach ($ingredientRepo->findAll() as $ing) {
            $ingredients[$ing->getId()] = $ing->getNom();
        }

        return $this->render('panier/index.html.twig', [
            'items' => $items,
            'total' => $total,
            'ingredients' => $ingredients
        ]);
    }


    /* -----------------------------------------
       AJOUT SIMPLE AU PANIER (SANS PERSONNALISATION)
    ----------------------------------------- */
    #[Route('/panier/add/{id}', name: 'panier_add')]
    public function add(int $id, Request $request): JsonResponse
    {
        $session = $request->getSession();
        $panier = $session->get('panier', []);

        $panier[] = [
            'id' => $id,
            'quantity' => 1,
            'removed' => [],
            'replaced' => []
        ];

        $session->set('panier', $panier);

        return $this->json([
            'success' => true,
            'count' => count($panier)
        ]);
    }


    /* -----------------------------------------
       AJOUT AVEC PERSONNALISATION
    ----------------------------------------- */
    #[Route('/panier/add-custom/{id}', name: 'panier_add_custom', methods: ['POST'])]
    public function addCustom(
        int $id,
        PlatRepository $platRepo,
        Request $request
    ): JsonResponse {

        $plat = $platRepo->find($id);
        if (!$plat) {
            return new JsonResponse(['error' => 'Plat introuvable'], 404);
        }

        $data = json_decode($request->getContent(), true);

        $removed = $data['removed'] ?? [];
        $replaced = $data['replaced'] ?? [];

        $session = $request->getSession();
        $panier = $session->get('panier', []);

        $panier[] = [
            'id' => $id,
            'quantity' => 1,
            'removed' => $removed,
            'replaced' => $replaced
        ];

        $session->set('panier', $panier);

        return new JsonResponse([
            'success' => true,
            'count' => count($panier)
        ]);
    }


    /* -----------------------------------------
       AUGMENTER QUANTITÉ
    ----------------------------------------- */
    #[Route('/panier/plus/{index}', name: 'panier_plus')]
    public function plus(int $index, Request $request): Response
    {
        $session = $request->getSession();
        $panier = $session->get('panier', []);

        if (isset($panier[$index])) {
            $panier[$index]['quantity']++;
        }

        $session->set('panier', $panier);
        return $this->redirectToRoute('panier');
    }


    /* -----------------------------------------
       DIMINUER QUANTITÉ
    ----------------------------------------- */
    #[Route('/panier/minus/{index}', name: 'panier_minus')]
    public function minus(int $index, Request $request): Response
    {
        $session = $request->getSession();
        $panier = $session->get('panier', []);

        if (isset($panier[$index])) {

            if ($panier[$index]['quantity'] > 1) {
                $panier[$index]['quantity']--;
            } else {
                unset($panier[$index]);
                $panier = array_values($panier);
            }
        }

        $session->set('panier', $panier);
        return $this->redirectToRoute('panier');
    }


    /* -----------------------------------------
       SUPPRIMER UN ITEM
    ----------------------------------------- */
    #[Route('/panier/remove/{index}', name: 'panier_remove')]
    public function remove(int $index, Request $request): Response
    {
        $session = $request->getSession();
        $panier = $session->get('panier', []);

        if (isset($panier[$index])) {
            unset($panier[$index]);
            $panier = array_values($panier);
        }

        $session->set('panier', $panier);
        return $this->redirectToRoute('panier');
    }
}
