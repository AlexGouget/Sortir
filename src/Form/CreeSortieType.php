<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Ville;
use ContainerFQfy3pB\getCampusRepositoryService;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreeSortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label'=>'Nom de la sortie :'
            ])
            ->add('dateHeureDebut', DateTimeType::class, [
                'label'=>'Date et heure de la sortie :',
                'html5'=>true,
            ])
            ->add('dateLimiteInscription', DateTimeType::class, [
                'label'=>'Date limite d\'inscription :',
                'html5'=>true,
            ])
            ->add('duree' , IntegerType::class, [
                'label'=>'Durée (en minutes) :'
            ])
            ->add('nbInscriptionMax', IntegerType::class, [
                'label'=>'Nombre de places :'
            ])
            ->add('infosSortie', TextareaType::class, [
                'label'=>'Description et infos :'
            ])
            ->add('campus',EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'nom',
                  'label'=>'Campus :'
            ])

        ->add('lieu',EntityType::class, [
        'class' => Lieu::class,
        'choice_label' => 'nom',
            'label'=>'Lieu :'
    ]);



    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
            "allow_extra_fields" => true,
        ]);
    }
}
