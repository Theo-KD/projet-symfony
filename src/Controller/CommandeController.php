<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Repository\PlatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommandeController extends AbstractController
{
    #[Route('/commande', name: 'commande')]
    public function commande(Request $request, PlatRepository $platRepository): Response
    {
        $session = $request->getSession();
        $panier = $session->get('panier', []);

        if (empty($panier)) {
            return $this->redirectToRoute('panier');
        }

        $items = [];
        $total = 0;

        foreach ($panier as $entry) {

            $platId   = $entry['id'];        // ✔️ correction
            $quantity = $entry['quantity'];  // ✔️ correction

            $plat = $platRepository->find($platId);

            if ($plat) {

                $prixTotal = $plat->getPrix() * $quantity;

                $items[] = [
                    'plat'     => $plat,
                    'quantity' => $quantity,
                    'total'    => $prixTotal
                ];

                $total += $prixTotal;
            }
        }

        $user = $this->getUser();
        $peutReduc = $user && $user->getPoints() >= 1000;

        return $this->render('commande/index.html.twig', [
            'items'     => $items,
            'total'     => $total,
            'peutReduc' => $peutReduc
        ]);
    }


    #[Route('/commande/valider', name: 'commande_valider', methods: ['POST'])]
    public function valider(
        Request $request,
        PlatRepository $platRepository,
        EntityManagerInterface $em
    ): Response {

        $session = $request->getSession();
        $panier = $session->get('panier', []);

        if (empty($panier)) {
            return $this->redirectToRoute('panier');
        }

        $numero     = $request->request->get('numero');
        $expiration = $request->request->get('expiration');
        $cvv        = $request->request->get('cvv');

        if (!$numero || !$expiration || !$cvv) {
            $this->addFlash('error', 'Veuillez remplir toutes les informations bancaires.');
            return $this->redirectToRoute('commande');
        }

        $total = 0;
        $pointsGagnes = 0;

        foreach ($panier as $entry) {

            $platId   = $entry['id'];        // ✔️ correction
            $quantity = $entry['quantity'];  // ✔️ correction

            $plat = $platRepository->find($platId);

            if ($plat) {

                $prix = $plat->getPrix() * $quantity;
                $total += $prix;

                $slug = $plat->getCategorie()->getSlug();

                switch ($slug) {
                    case 'plats':
                        $pointsGagnes += 150 * $quantity;
                        break;

                    case 'desserts':
                        $pointsGagnes += 200 * $quantity;
                        break;

                    case 'entrees':
                        $pointsGagnes += 100 * $quantity;
                        break;

                    case 'boissons':
                        $pointsGagnes += 50 * $quantity;
                        break;
                }
            }
        }

        $user = $this->getUser();
        $reductionActive = $request->request->get('reduction') === '1';

        if ($reductionActive && $user->getPoints() >= 1000) {

            $reduc = $total * 0.10;
            $max   = $total * 0.20;

            if ($reduc > $max) {
                $reduc = $max;
            }

            $total -= $reduc;

            $user->setPoints($user->getPoints() - 1000);
        }

        $user->setPoints($user->getPoints() + $pointsGagnes);
        $user->setTotalCommandes($user->getTotalCommandes() + 1);

        $commande = new Commande();
        $commande->setClient($user);
        $commande->setTotal($total);
        $commande->setCreatedAt(new \DateTimeImmutable());
        $commande->setStatus('en_attente');

        $em->persist($commande);
        $em->persist($user);
        $em->flush();

        $session->remove('panier');

        $this->addFlash('success', 'Commande validée !');

        return $this->redirectToRoute('home');
    }
}
