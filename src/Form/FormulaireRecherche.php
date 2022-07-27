<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Categorie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class FormulaireRecherche extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('query', TextType::class, [
            'empty_data'=> '',
            'label' => false,
            'required'=>false,
            'attr' => [
                'class' => 'form-control custom-form-control-lg custom-form-control-main',
                'placeholder' => 'évènement, lieu, ville...',
                'id'=>'research'
            ]
        ])

            ->add('campus',EntityType::class, [
                'attr' => [
                    'class' => 'form-control',

                ],
                'class' => Campus::class,
                'required'=>false,
                'choice_label' => 'nom',
                'placeholder' => 'Selectionner un campus'
            ])
            ->add('categorie',EntityType::class, [
                'attr' => [
                    'class' => 'form-control',

                ],
                'class' => Categorie::class,
                'required'=>false,
                'choice_label' => 'libelleCat',
                'placeholder' => 'Toutes les catégories'
            ])
            ->add('dateDebut', DateTimeType::class, [
                'attr' => [
                    'class' => 'form-control',

                ],
                'widget' => 'single_text',
                'label'=>'Entre',
                'required'=>false,
                'input'=> 'datetime',
                'input_format'=>'datetime'

            ])
            ->add('dateFin', DateTimeType::class, [
                'attr' => [
                    'class' => 'form-control',

                ],
                'widget' => 'single_text',
                'label'=>'et',
                'required'=>false,
                'html5'=>true,

            ])
            ->add('CBorganisateur', CheckboxType::class, [
                'label'=>"Sorties dont je suis l'organisateur/trice",
                'required'=>false,

            ])
            ->add('CBinscrit', CheckboxType::class, [
                'label'=>"Sorties auxquelle je suis inscrit/e",
                'required'=>false,
            ])
            ->add('CBnonInscrit', CheckboxType::class, [
                'label'=>"Sorties auxquelle je ne suis pas inscrit/e",
                'required'=>false,
            ])
            ->add('CBarchive', CheckboxType::class, [
                'label'=>"Sorties passées",
                'required'=>false,
            ])->add('submit', SubmitType::class,[
                'label'=>' ',
                'attr'=>[
                    'class'=>'rounded btn btn-lg btn-primary ti-search'
                ]
            ]);
    }


}