<?php
namespace App\Form;

use App\Entity\Adherant;
use App\Entity\Categorie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdherantPoidsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) :void
    {
        $builder
            ->add('adherants', EntityType::class, [
                'class' => Adherant::class,
                'choice_label' => 'nom',
                'placeholder' => 'Sélectionnez un adhérent',
            ])
            ->add('poids', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'categorie_poids',
                'placeholder' => 'Sélectionnez une catégorie de poids',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Adherant::class,
        ]);
    }
}
