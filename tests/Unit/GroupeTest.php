<?php
namespace Unit;

use App\Entity\User;
use App\Entity\Combat;
use App\Entity\Groupe;
use App\Entity\Tournoi;
use App\Entity\Adherant;
use App\Form\CombatType;
use App\Entity\Categorie;
use App\Service\CombatService;
use App\Service\TournoiService;
use PHPUnit\Framework\TestCase;
use App\Entity\CategorieTournoi;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class GroupeTest extends TestCase
{

    private $em;
    private $security;
    private $tournoiService;

    protected function setUp(): void
    {
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->security = $this->createMock(Security::class);
        $this->tournoiService = new CombatService($this->em);
    }

    public function testCreerGroupesParCategorieAvecGroupesDejaGeneres()
    {
        $tournoi = $this->createMock(Tournoi::class);
        $tournoi->method('getGroupes')->willReturn(new ArrayCollection([new Groupe()]));

        $result = $this->tournoiService->creerGroupesParCategorie($tournoi, $this->security);

        $this->assertEmpty($result);
    }

    public function testCreerGroupesParCategorieSansAutorisation()
    {
        $tournoi = $this->createMock(Tournoi::class);
        $president = $this->createMock(User::class);
        $tournoi->method('getPresident')->willReturn($president);

        $this->security->method('getUser')->willReturn($this->createMock(User::class));

        $this->expectException(AccessDeniedHttpException::class);

        $this->tournoiService->creerGroupesParCategorie($tournoi, $this->security);
    }

    public function testCreerGroupesParCategorieMoinsDe16Combattants()
    {
        $tournoi = $this->createMock(Tournoi::class);
        $categorieTournoi = $this->createMock(CategorieTournoi::class);
        $categorie = $this->createMock(Categorie::class);
        $president = $this->createMock(User::class);
    
        // Configurer le président du tournoi
        $tournoi->method('getPresident')->willReturn($president);
        $this->security->method('getUser')->willReturn($president);
        $this->security->method('isGranted')->willReturn(true);
    
        // Configurer la catégorie avec moins de 16 combattants
        $combattants = array_fill(0, 10, $this->createMock(Adherant::class));
        $categorieTournoi->method('getCombattants')->willReturn(new ArrayCollection($combattants));
        $categorieTournoi->method('getCategorie')->willReturn($categorie);
    
        // Ajouter cette catégorie au tournoi
        $tournoi->method('getCategorieTournois')->willReturn(new ArrayCollection([$categorieTournoi]));
    
        // Appel de la méthode testée
        $result = $this->tournoiService->creerGroupesParCategorie($tournoi, $this->security);
    
        // Vérifier qu'aucun groupe n'a été créé
        $this->assertEmpty($result, "Aucun groupe ne doit être créé pour une catégorie avec moins de 16 combattants.");
    }
    
    public function testCreerGroupesParCategorieAvec16Combattants()
    {
        $tournoi = $this->createMock(Tournoi::class);
        $categorieTournoi = $this->createMock(CategorieTournoi::class);
        $categorie = $this->createMock(Categorie::class);
        $president = $this->createMock(User::class);
    
        // Configure la catégorie
        $categorie->method('getCategoriePoids')->willReturn(60.0);
        $categorieTournoi->method('getCategorie')->willReturn($categorie);
    
        // Configure le tournoi et son président
        $tournoi->method('getPresident')->willReturn($president);
        $this->security->method('getUser')->willReturn($president);
        $this->security->method('isGranted')->willReturn(true);
    
        // Configure les combattants
        $combattants = [];
        for ($i = 0; $i < 16; $i++) {
            $combattants[] = $this->createMock(Adherant::class);
        }

        $categorieTournoi->method('getCombattants')
            ->willReturn(new ArrayCollection($combattants));
        $tournoi->method('getCategorieTournois')
            ->willReturn(new ArrayCollection([$categorieTournoi]));
    
        $this->em
            ->expects($this->exactly(28)) // 24 combats + 4 groupes
            ->method('persist')
            ->with($this->callback(function ($entity) {
                return $entity instanceof Combat || $entity instanceof Groupe;
            }));
    
            $this->em
            ->expects($this->atLeastOnce())
            ->method('flush');
            
        // Appel de la méthode testée
        $result = $this->tournoiService->creerGroupesParCategorie(
            $tournoi, $this->security);
    
        // Vérifications
        $this->assertNotEmpty($result, 
          "La méthode creerGroupesParCategorie a retourné un tableau vide.");
        $this->assertCount(4, $result, 
          "La méthode n'a pas créé exactement 4 groupes.");
    }
    
    

    public function testCreerGroupesParCategorieAvecPlusDe16Combattants()
    {
        $tournoi = $this->createMock(Tournoi::class);
        $categorieTournoi = $this->createMock(CategorieTournoi::class);
        $categorie = $this->createMock(Categorie::class);
        $president = $this->createMock(User::class);

        // Configurer le président du tournoi
        $tournoi->method('getPresident')->willReturn($president);
        $this->security->method('getUser')->willReturn($president);
        $this->security->method('isGranted')->willReturn(true);

        // Configurer une catégorie avec plus de 16 combattants
        $combattants = [];
        for ($i = 0; $i < 20; $i++) {
            $combattants[] = $this->createMock(Adherant::class);
        }
        $categorieTournoi->method('getCombattants')
            ->willReturn(new ArrayCollection($combattants));
        $categorieTournoi->method('getCategorie')
            ->willReturn($categorie);

        // Ajouter cette catégorie au tournoi
        $tournoi->method('getCategorieTournois')
            ->willReturn(new ArrayCollection([$categorieTournoi]));

 
        $this->em
        ->expects($this->atLeastOnce())
        ->method('flush');

        // Appel de la méthode testée
        $result = $this->tournoiService->creerGroupesParCategorie($tournoi, $this->security);

        // Vérifications
        $this->assertNotEmpty($result, "La méthode creerGroupesParCategorie a 
          retourné un tableau vide alors qu'il y a plus de 16 combattants.");
        $this->assertCount(4, $result, "La méthode n'a pas créé exactement 4 groupes.");
        $totalCombattants = array_reduce($result, fn($carry, $groupe) 
            => $carry + count($groupe->getCombattants()), 0);
        $this->assertEquals(16, $totalCombattants, 
            "Le total des combattants dans les groupes n'est pas correct.");
    }
}
