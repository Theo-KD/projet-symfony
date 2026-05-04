<?php

namespace App\Controller;

use App\Repository\CommandeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/compte', name: 'app_compte')]
    public function compte(CommandeRepository $commandeRepo): Response
    {
        $user = $this->getUser();

        // Récupérer les commandes du client
        $commandes = $commandeRepo->findBy(['client' => $user]);

        return $this->render('user/compte.html.twig', [
            'user' => $user,
            'commandes' => $commandes,
            'nbCommandes' => count($commandes),
        ]);
    }
}
