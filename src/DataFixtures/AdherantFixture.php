<?php

namespace App\DataFixtures;

use App\Entity\Adherant;
use App\Entity\Club;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker\Factory as FakerFactory;


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

        for ($i = 0; $i < 50; $i++) {
            // Créer un nouvel adhérant
            $adherant = new Adherant();
            $adherant->setNom($faker->lastName());
            $adherant->setPrenom($faker->firstName());
            $adherant->setEmail($faker->email());

            // Générer un mot de passe hashé
            $password = $this->passwordHasher->hashPassword($adherant, 'test12');
            $adherant->setPassword($password);

            // Assigner un club_id aléatoire (1, 2 ou 3)
            $clubId = [1, 5, 6][array_rand([1, 5, 6])];
            $club = $manager->getRepository(Club::class)->find($clubId);
            if ($club) {
                $adherant->setClub($club);
            }

            // Ajouter un rôle (optionnel)
            $adherant->setRoles(['ROLE_ADHERANT']);

            // Persist l'adhérant dans la base de données
            $manager->persist($adherant);
        }

        // Sauvegarder les adhérants en base
        $manager->flush();
    }
}
