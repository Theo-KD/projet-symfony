<?php

namespace App\DataFixtures;

use App\Entity\Categorie;
use App\Entity\Plat;
use App\Entity\Ingredient;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // --- CATEGORIES ---
        $categoriesData = [
            ['Entrées', 'entrees'],
            ['Plats', 'plats'],
            ['Desserts', 'desserts'],
            ['Boissons', 'boissons'],
        ];

        $categories = [];

        foreach ($categoriesData as [$nom, $slug]) {
            $cat = new Categorie();
            $cat->setNom($nom);
            $cat->setSlug($slug);
            $manager->persist($cat);
            $categories[$slug] = $cat;
        }

        // --- PLATS ---
        $platsData = [
            // Entrées
            ['Nems au poulet', 'Rouleaux croustillants farcis au poulet', 5.50, 'nems-p.jpeg', 'entrees', true, [
                ['Poulet', true],
                ['Carottes', false],
                ['Chou', false],
            ]],
            ['Raviolis vapeur', 'Délicats raviolis au porc', 6.00, 'raviolis.jpeg', 'entrees', true, [
                ['Porc', true],
                ['Gingembre', false],
            ]],
            ['Soupe pékinoise', 'Soupe traditionnelle légèrement épicée', 4.50, 'soupe.jpeg', 'entrees', true, [
                ['Bouillon', true],
                ['Poulet', false],
            ]],
            ['Salade de chou', 'Chou chinois croquant et vinaigrette', 4.00, 'salade-chou-25780.jpg', 'entrees', true, [
                ['Chou', true],
                ['Sésame', false],
            ]],
            ['Beignets de crevettes', 'Crevettes croustillantes', 6.50, 'crevettes.jpg', 'entrees', true, [
                ['Crevettes', true],
            ]],

            // Plats
            ['Canard laqué', 'Canard rôti façon pékinoise', 14.90, 'canard.webp', 'plats', true, [
                ['Canard', true],
                ['Sauce laquée', true],
            ]],
            ['Poulet croustillant au sésame', 'Poulet frit nappé de sauce sucrée', 12.50, 'Poulet-s.jpg', 'plats', true, [
                ['Poulet', true],
                ['Sésame', false],
            ]],
            ['Bœuf sauté aux oignons', 'Bœuf tendre et oignons caramélisés', 13.00, 'Boeuf.webp', 'plats', true, [
                ['Bœuf', true],
                ['Oignons', true],
            ]],
            ['Nouilles sautées', 'Nouilles aux légumes croquants', 10.00, 'nouilles.jpg', 'plats', true, [
                ['Nouilles', true],
                ['Légumes', false],
            ]],
            ['Riz cantonais', 'Riz sauté aux œufs et jambon', 9.50, 'riz-cantonais-veggie.webp', 'plats', true, [
                ['Riz', true],
                ['Œufs', false],
                ['Jambon', false],
            ]],

            // Desserts
            ['Perles de coco', 'Boules de coco fondantes', 4.50, 'perles.jpg', 'desserts', false, []],
            ['Beignets d’ananas', 'Ananas frits croustillants', 4.00, 'beignets-ananas.webp', 'desserts', false, []],
            ['Litchis au sirop', 'Litchis frais en sirop', 3.50, 'siros-lit.jpeg', 'desserts', false, []],
            ['Nougat chinois', 'Nougat traditionnel aux cacahuètes', 3.00, 'nougat.jpg', 'desserts', false, []],
            ['Boule coco glacée', 'Glace coco artisanale', 4.20, 'coconuticecream.webp', 'desserts', false, []],

            // Boissons (non personnalisables)
            ['Thé au jasmin', 'Thé parfumé traditionnel', 2.50, 'thé-jasmin.jpg', 'boissons', false, []],
            ['Soda lychee', 'Boisson gazeuse au litchi', 3.00, 'lychee.jpg', 'boissons', false, []],
            ['Bubble tea taro', 'Boisson lactée au taro', 5.00, 'taro.jpg', 'boissons', false, []],
            ['Eau minérale', 'Eau fraîche', 1.50, 'eau.webp', 'boissons', false, []],
            ['Thé glacé maison', 'Thé glacé fait maison', 3.50, 'maison.jpg', 'boissons', false, []],
        ];

        foreach ($platsData as [$nom, $desc, $prix, $image, $catSlug, $personnalisable, $ingredients]) {
            $plat = new Plat();
            $plat->setNom($nom);
            $plat->setDescriptionCourte($desc);
            $plat->setPrix($prix);
            $plat->setImage($image);
            $plat->setCategorie($categories[$catSlug]);
            $plat->setDisponible(true);
            $plat->setPersonnalisable($personnalisable);

            foreach ($ingredients as [$ingNom, $essentiel]) {
                $ing = new Ingredient();
                $ing->setNom($ingNom);
                $ing->setEssentiel($essentiel);
                $ing->setPlat($plat);
                $manager->persist($ing);
            }

            $manager->persist($plat);
        }

        $manager->flush();
    }
}
