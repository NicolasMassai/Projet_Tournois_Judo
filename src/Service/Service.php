<?php
namespace App\Service;

use App\Entity\Club;
use App\Entity\User;
use App\Form\ClubType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class Service extends AbstractController
{   
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;        
    }

    public function createClubWithPresident(Request $request, User $user): Response
    {
        $club = new Club();
        $form = $this->createForm(ClubType::class, $club);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Définir l'utilisateur comme président du club
            $club->setPresident($user);
    
            // Effacer tous les rôles existants et attribuer uniquement ROLE_PRESIDENT
            $user->setRoles(['ROLE_PRESIDENT']);
    
            $this->em->persist($club);
            $this->em->persist($user); // Persist les changements de rôle
            $this->em->flush();
    
            return $this->redirectToRoute('app_club');
        }
    
        return $this->render('club/club_create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
    
    public function create(Request $request,$Class, $ClassType, $string ,$string2, $string3 ): Response
    {
        $var = new $Class();
        $form = $this->createForm($ClassType::class, $var);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($var);
            $this->em->flush();
            return $this->redirectToRoute('app_'.$string.'');
        }

        return $this->render(''.$string2.'/'.$string3.'_create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    public function update(Request $request , $ClassType , $class, $string, $string2, $string3): Response
    {
        $form = $this->createForm($ClassType::class, $class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($class);
            $this->em->flush();
            return $this->redirectToRoute('app_'.$string.'');
        }
        return $this->render(''.$string2.'/'.$string3.'_update.html.twig', [
            'form' => $form->createView()
        ]);
    }
   

}