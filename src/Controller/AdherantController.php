<?php

namespace App\Controller;

use App\Entity\Adherant;
use App\Service\Service;
use App\Form\AdherantType;
use App\Repository\AdherantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\ByteString;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdherantController extends AbstractController
{


    private EntityManagerInterface $em;


    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/adherant', name: 'app_adherant')]
    public function adherant(AdherantRepository $adherantRepository): Response
    {
        $adherant = $adherantRepository->findAll();


        return $this->render('adherant/adherant.html.twig', [
            'adherants' => $adherant
        ]);
    }


    #[Route('/adherant/create', name: 'app_adherant_create')]
    public function create(Service $myService, Request $request): Response
    {
        $form = $myService->create(
            $request,
            new Adherant,
            new AdherantType,
            new ByteString('adherant'),
            new ByteString('adherant'),
            new ByteString('adherant')
        );

        return $form;
    }

    #[Route('/adherant/update/{adherant}', name: 'app_adherant_update')]
    public function update(Service $myService, Adherant $adherant, Request $request): Response
    {

        $form = $myService->update(
            $request,
            new AdherantType,
            $adherant,
            new ByteString('adherant'),
            new ByteString('adherant'),
            new ByteString('adherant')
        );

        return $form;

    }

    #[Route('/adherant/delete/{adherant}', name: 'app_adherant_delete')]
    public function delete(Adherant $adherant): Response

    {
               
        $this->em->remove($adherant);
        $this->em->flush();

        return $this->redirectToRoute("app_adherant");
        }
} 
