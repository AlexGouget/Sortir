<?php

namespace App\Form\User;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pseudo', TextType::class,[
                'label' => 'Pseudo :'
            ])
            ->add('email', EmailType::class,[
                'label' => 'Email :'
            ])
            ->add('nom', TextType::class, [
                'label'=>'Nom :'
            ])
            ->add('prenom', TextType::class, [
                'label'=>'Prenom :'
            ])
            ->add('telephone', TelType::class,['label'=>'Téléphone'])
            /*->add('campus',EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'nom',
                'label'=>'Campus :'
            ])*/
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
