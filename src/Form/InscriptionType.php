<?php

namespace App\Form;

use App\Entity\Club;
use App\Entity\Tournoi;
use App\Entity\Adherant;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class InscriptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('combattant', EntityType::class, [
                'class' => Adherant::class,
                'choices' => $options['club_adherants'], // membres du club seulement
                'choice_label' => function(Adherant $adherant) {
                    return $adherant->getNom() . ' ' . $adherant->getPrenom();
                },
                'multiple' => true,
                'expanded' => true, // Pour afficher les membres comme des cases à cocher
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Inscrire au tournoi',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tournoi::class,
            'club_adherants' => [] // On passe les membres du club en option
        ]);
    }
}
