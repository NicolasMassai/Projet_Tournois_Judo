<?php

namespace App\Controller;

use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{

    private EntityManagerInterface $em;


    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/profil', name: 'app_profil')]
    public function profil(): Response
    {
        $user = $this->getUser(); // Récupère l'utilisateur connecté

        return $this->render('user/index.html.twig', [
            'user' => $user,
        ]);
    }
    
    #[Route('/profil/update', name: 'app_profil_update')]
    public function update(Request $request): Response
    {
        $user = $this->getUser();
        
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->em->persist($user);
            $this->em->flush();

            return $this->redirectToRoute('app_profil');
        }

        return $this->render('user/update.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/profil/supprimer', name: 'app_profil_delete', methods: ['POST'])]
    public function delete(Request $request): Response
    {
        $user = $this->getUser();

        if (!$user) {
            throw $this->createNotFoundException('User not found.');
        }
    
        if ($user !== $this->getUser()) {
            throw $this->createAccessDeniedException('You are not allowed to delete this account.');
        }

        if ($this->isCsrfTokenValid('delete_user', $request->request->get('_token'))) {
            $this->em->remove($user);
            $this->em->flush();


            return $this->redirectToRoute('app_home');
        }

        return $this->redirectToRoute('app_profil');
    }
}
