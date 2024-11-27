<?php

namespace App\Controller;

use App\Entity\Club;
use App\Entity\User;
use App\Entity\Arbitre;
use App\Entity\Adherant;
use App\Entity\Spectateur;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager): Response
    {
        $adherant = new Adherant();
        $form = $this->createForm(RegistrationFormType::class, $adherant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $role = $form->get('role')->getData();

            if ($role === 'ROLE_ADHERANT') {
                $user = new Adherant();
                $clubId = $form->get('club')->getData(); // Récupérer l'ID du club depuis le formulaire
                $club = $entityManager->getRepository(Club::class)->find($clubId);

                if ($club) {
                    $user->setClub($club); // Associer le club
                    $club->addAdherant($user); // Mise à jour inverse
                }
            } elseif ($role === 'ROLE_ARBITRE') {
                $user = new Arbitre();
            } elseif ($role === 'ROLE_SPECTATEUR') {
                $user = new Spectateur();
            }

            $user->setPrenom($form->get('prenom')->getData());
            $user->setNom($form->get('nom')->getData());
            $user->setEmail($form->get('email')->getData());

            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            $user->setRoles([$role]);

            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email

            return $security->login($user, 'form_login', 'main');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}
