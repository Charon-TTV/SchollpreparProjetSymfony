<?php

namespace App\DataFixtures;

use App\Entity\Categorie;
use App\Entity\Etablissement;
use App\Entity\Filiere;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // On initialise Faker sans localisation spécifique pour plus de stabilité
        $faker = Factory::create();

        // 1. Création des Catégories
        $categories = [];
        $nomsCategories = ['Informatique', 'Gestion', 'Santé', 'Droit', 'Lettres'];

        foreach ($nomsCategories as $nom) {
            $categorie = new Categorie();
            $categorie->setNom($nom);
            $categorie->setDescription("Description pour la catégorie " . $nom);
            $manager->persist($categorie);
            $categories[] = $categorie;
        }

        // 2. Création des Filières
        $filieres = [];
        for ($i = 1; $i <= 10; $i++) {
            $filiere = new Filiere();
            $filiere->setNom("Filière test n°" . $i);
            $filiere->setDescription("Ceci est une description générée pour la filière test n°" . $i);
            $filiere->setImage('default-filiere.jpg');
            $filiere->setCategorie($faker->randomElement($categories));
            $manager->persist($filiere);
            $filieres[] = $filiere;
        }

        // 3. Création d'Établissements
        for ($i = 1; $i <= 8; $i++) {
            $etablissement = new Etablissement();
            $etablissement->setNom($faker->company);
            $etablissement->setVille($faker->city);
            $etablissement->setLocalisation($faker->address);
            $etablissement->setType('Privé');
            $etablissement->setImage('default-resto.jpg');

            // Association aléatoire de filières
            $randomFilieres = $faker->randomElements($filieres, rand(1, 3));
            foreach ($randomFilieres as $filiere) {
                $etablissement->addFiliere($filiere);
            }

            $manager->persist($etablissement);
        }

        $manager->flush();
    }
}
