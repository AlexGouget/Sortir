<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pseudo', TextType::class,[
                'label' => 'Pseudo :',
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
            ->add('email', EmailType::class,[
                'label' => 'Email :',
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
            ->add('nom', TextType::class, [
                'label'=>'Nom :',
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
            ->add('prenom', TextType::class, [
                'label'=>'Prenom :',
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
            ->add('telephone', TelType::class,[
                'label'=>'Téléphone',
                'attr' => [
                    'class' => 'form-control',
                ]
            ])

            ->add('campus',EntityType::class, [
                'class' => Campus::class,
                'placeholder'=>'choissisez un campus',
                'choice_label' => 'nom',
                'label'=>'Campus :',
                'attr' => [
                    'class' => 'form-control',
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
