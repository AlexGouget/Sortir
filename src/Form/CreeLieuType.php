<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class CreeLieuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label'=>'Nom du lieu :'

            ])
            ->add('rue', TextType::class, [
                'label'=>'Nom de la rue :'
            ])
            ->add('latitude')
            ->add('longitude')
            ->add('ville', EntityType::class,[
                'class'=>Ville::class,
                'choice_label'=>'nom',
                'label'=>'Ville :'
            ])

            ->add('creeLieu', SubmitType::class,['label' => 'Cree un lieu'] )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Lieu::class,
        ]);
    }
}
