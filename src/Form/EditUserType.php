<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;


class EditUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('imageFile', VichImageType::class, [
                'download_label' => false,
                'allow_delete'=> false,
                'required'=> false,
                'label'=> 'Photo de profil: '
            ])
            ->add('pseudo', TextType::class,[
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('nom', TextType::class,[
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('prenom', TextType::class,[
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('telephone', TelType::class,[
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('campus', EntityType::class, [
                'class'=> Campus::class,
                'choice_label'=>'nom',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
