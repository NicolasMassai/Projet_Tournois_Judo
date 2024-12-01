<?php

namespace App\Service;


use App\Entity\Arbitre;
use App\Entity\Tournoi;
use App\Entity\CategorieTournoi;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AssignArbitre extends AbstractController
{

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }


    public function assignArbitres(Tournoi $tournoi, Request $request, Security $security): Response
    {

        // Vérifier que l'utilisateur connecté est le président du tournoi
        $user = $security->getUser();

        if (!$user || $user !== $tournoi->getPresident()) {
            $this->addFlash('error', 'Vous n\'êtes pas autorisé à assigner des arbitres pour ce tournoi.');
            return $this->redirectToRoute('app_home');
        }

        $arbitres = $this->em->getRepository(Arbitre::class)->findAll(); // Récupérer tous les arbitres
    
        $form = $this->createFormBuilder()
            ->add('categorieTournoi', EntityType::class, [
                'class' => CategorieTournoi::class,
                'choices' => $this->em->getRepository(CategorieTournoi::class)
                    ->findBy(['tournoi' => $tournoi]),
                'choice_label' => function (CategorieTournoi $categorieTournoi) {
                    return $categorieTournoi->getCategorie()->getCategoriePoids();
                },
            ])
            ->add('arbitres', EntityType::class, [
                'class' => Arbitre::class,
                'choices' => $arbitres,
                'choice_label' => function (Arbitre $arbitre) {
                    return $arbitre->getNom() . ' ' . $arbitre->getPrenom();
                },
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('Assigner', SubmitType::class)
            ->getForm();
    
            $form->handleRequest($request);
    
            if ($form->isSubmitted() && $form->isValid()) {
                $categorieTournoi = $form->get('categorieTournoi')->getData();
                $selectedArbitres = $form->get('arbitres')->getData();
        
                foreach ($selectedArbitres as $arbitre) {
                    $categorieTournoi->addArbitre($arbitre);
                }
        
                $this->em->persist($categorieTournoi);
                $this->em->flush();
        
                return $this->redirectToRoute('app_tournoi_show', ['id' => $tournoi->getId()]);
            }
    
        return $this->render('tournoi/arbitres.html.twig', [
            'form' => $form->createView(),
            'tournoi' => $tournoi,
        ]);
    }

}