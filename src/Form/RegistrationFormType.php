<?php

namespace App\Form;

use App\Entity\Club;
use App\Entity\User;
use App\Entity\Categorie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class RegistrationFormType extends AbstractType
{

    private RouterInterface $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }



    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('prenom', TextType::class, [
                'label' => 'Prénom'
            ])
            ->add('nom', TextType::class, [
                'label' => 'Nom'
            ])
            ->add('role', ChoiceType::class, [
                'choices'  => [
                    'Adherant' => 'ROLE_ADHERANT',
                    'Arbitre'  => 'ROLE_ARBITRE',
                    'Spectateur'  => 'ROLE_SPECTATEUR',
                ],
                'mapped' => false,
                'label' => 'Inscription en tant que',
                'expanded' => true,
                'multiple' => false,
            ])
            ->add('club', EntityType::class, [
                'class' => Club::class,
                'choice_label' => 'nom', // Ou tout autre champ que tu veux afficher
                'placeholder' => 'Sélectionnez un club',
                'required' => false,
            ])
            ->add('categorie', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'categoriePoids', // afficher la catégorie de poids comme libellé
                'required' => false, // optionnel, à afficher selon le rôle
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez accepter les mentions légales pour continuer.',
                    ]),
                ],
                'label' => 'J\'ai pris connaissance des <a href="' . $this->router->generate('app_mentions_legales') . '" target="_blank">mentions légales</a>.',
                'label_html' => true,

            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
