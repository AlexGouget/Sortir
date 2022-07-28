<?php

namespace App\Form\Lieu;

use App\Entity\Lieu;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class CreeLieuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'attr' => [
                    'class' => 'lieu-control form-control',
                     'placeholder' => 'nom du lieu',
                 ]

            ])
            ->add('rue', TextType::class, [
                'attr' => [
                    'class' => 'lieu-control form-control',
                    'placeholder' => 'Adresse  du lieu',
                ],
                'required'=>false,
            ])
            ->add('latitude', NumberType::class,[
                'attr' => [
                    'class' => 'lieu-control form-control',
                    'placeholder' => 'lat...',
                ],
                'required'=>false
            ])
            ->add('longitude', NumberType::class,[
                'attr' => [
                    'class' => 'lieu-control form-control',
                    'placeholder' => 'long',
                ],
                'required'=>false
            ])
            ->add('ville', EntityType::class,[
                'class'=>Ville::class,
                'choice_label'=>'nom',
                'placeholder'=>'choisissez une ville',
                'label'=>'Ville :',
                'required'=>false,
                'attr' => [
                    'class' => 'lieu-control form-control',
                    'placeholder' => 'Ville',
                ],
            ])

            //->add('creeLieu', SubmitType::class,['label' => 'Cree un lieu'] )
        ;

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Lieu::class,
        ]);
    }
}