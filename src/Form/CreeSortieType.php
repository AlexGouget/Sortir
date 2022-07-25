<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Categorie;
use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreeSortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('nom', TextType::class, [
                'label'=>'Nom de la sortie :',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' =>  'Sortie en boite de nuit..',

                ]
            ])
            ->add('dateHeureDebut', DateTimeType::class, [
                'label'=>'Date et heure de la sortie :',
                'widget' => 'single_text',
                'html5'=>true,
                'attr' => [
                    'class' => 'form-control'
                ]

            ])
            ->add('dateLimiteInscription', DateTimeType::class, [
                'label'=>'Date limite d\'inscription :',
                'widget' => 'single_text',

                'html5'=>true,
                'attr' => [
                    'class' => 'form-control'
                ]

            ])
            ->add('duree' , IntegerType::class, [
                'label'=>'Durée (en minutes) :',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder'=> '60 minutes'
                ]
            ])
            ->add('nbInscriptionMax', IntegerType::class, [
                'label'=>'Nombre de places :',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder'=> '1,2,3....150 c\'est vous le chef !'
                ]
            ])
            ->add('infosSortie', TextareaType::class, [
                'label'=>'Description et infos :',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder'=> 'Decris ton activité en quelques mots pour donner envie aux autres étudiants'
                ]
            ])
            ->add('campus',EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'nom',
                  'label'=>'Campus :',
                'attr' => [
                    'class' => 'form-control',
                   ]
            ])

            ->add('categorie',EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'libelle_cat',
                'label'=>'Categorie :',
                'attr' => [
                    'class' => 'form-control',
                ]
            ])

        ->add('lieu',EntityType::class, [
            'label'=>'lieu :',
        'class' => Lieu::class,
        'choice_label' => 'nom',
            'label'=>'Lieu :',
            'placeholder'=>'choissisez un lieu',
            'attr' => [
                'class' => 'form-control',
                'placeholder' => 'évènement, lieu, ville...',
                'id'=>'choixLieu'
            ]

    ])->add('newLieu', CreeLieuType::class,[
                'required' => FALSE,
                'mapped' => FALSE,
                'property_path' => 'lieu',
            ])



        ->add('enregistrer', SubmitType::class,[
            'label' => 'Enregistrer',
            'attr' => [
                'class' => 'btn btn-primary',
            ]
        ] )
        ->add('publier', SubmitType::class,[
            'label' => 'Publier la sortie',
            'attr' => [
                'class' => 'btn btn-primary',
            ]
        ] )
        ->add('annuler', SubmitType::class,[
            'label' => 'Annuler',
            'attr' => [
                'class' => 'btn btn-primary',
            ]
        ] );


        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
                function(FormEvent $events)
                {
                    $form = $events->getForm();
                    $data = $events->getData();
                   // dd($data);
                    if (!empty($data['newLieu'])) {


                        $form->remove('lieu');

                        $form->add('newLieu', CreeLieuType::class, array(
                            'required' => TRUE,
                            'mapped' => TRUE,
                            'property_path' => 'lieu',
                        ));

                    }

                }
        );


    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
            "allow_extra_fields" => true,

        ]);
    }
}
