<?php

namespace App\Tests\Service;

use App\Entity\Combat;
use App\Service\CombatService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CombatService_Test extends WebTestCase
{
    private CombatService $combatService;
    private $client;
    private EntityManagerInterface $em;

    // Test de la création de combats existants
    public function testCreerCombatsWithExistingCombats()
    {
        // Crée le client et accède au conteneur de services
        $this->client = static::createClient();
        $this->em = self::getContainer()->get(EntityManagerInterface::class); // Utilisation de getContainer()
        $this->combatService = self::getContainer()->get(CombatService::class); // Initialisation de CombatService

        // Crée un combat pour simuler un combat existant
        $combat = new Combat();
        $combat->setResultat('Gagné');
        
        // Persiste le combat dans la base de données
        $this->em->persist($combat);
        $this->em->flush();

        // Vérifie que le combat a bien été ajouté
        $combatRepository = $this->em->getRepository(Combat::class);
        $combatInDb = $combatRepository->findOneBy(['resultat' => 'Gagné']);
        $this->assertNotNull($combatInDb, 'Le combat a bien été ajouté');
    }

    /*
    public function testCreerCombatsWithNoExistingCombats()
    {
        // Crée le client et accède au conteneur de services
        $this->client = static::createClient();
        $this->combatService = self::getContainer()->get(CombatService::class);
        $this->em = self::getContainer()->get(EntityManagerInterface::class); // Initialisation correcte de l'EntityManager

        // Appelle la méthode pour créer des combats
        $this->combatService->creerCombats();

        // Vérifie si des combats ont été créés
        $combatRepository = $this->em->getRepository(Combat::class);
        $combats = $combatRepository->findAll();
        $this->assertCount(1, $combats, 'Des combats ont été créés');
    }*/
}
