<?php

namespace App\DataFixtures;

use App\Entity\Adherant;
use App\Entity\Categorie;
use App\Entity\Club;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as FakerFactory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdherantFixture extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = FakerFactory::create(); // Initialize Faker

        // Récupérer toutes les catégories existantes
        $categories = $manager->getRepository(Categorie::class)->findAll();

        if (empty($categories)) {
            throw new \Exception("Aucune catégorie trouvée en base. Veuillez en créer au préalable.");
        }

        // Créer des adhérents
        for ($i = 0; $i < 50; $i++) {
            // Créer un nouvel adhérant
            $adherant = new Adherant();
            $adherant->setNom($faker->lastName());
            $adherant->setPrenom($faker->firstName());
            $adherant->setEmail($faker->email());

            // Générer un mot de passe hashé
            $password = $this->passwordHasher->hashPassword($adherant, 'test12');
            $adherant->setPassword($password);

            // Assigner un club_id aléatoire (1, 5 ou 6)
            $clubId = [1, 5, 6][array_rand([1, 5, 6])];
            $club = $manager->getRepository(Club::class)->find($clubId);
            if ($club) {
                $adherant->setClub($club);
            }

            // Assigner une catégorie aléatoire
            $categorie = $categories[array_rand($categories)];
            $adherant->setCategorie($categorie);

            // Ajouter un rôle (optionnel)
            $adherant->setRoles(['ROLE_ADHERANT']);

            // Persist l'adhérant dans la base de données
            $manager->persist($adherant);
        }

        // Sauvegarder les adhérents en base
        $manager->flush();
    }
}
