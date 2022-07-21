<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\Categorie;
use App\Form\RegistrationFormType;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EventResearchController extends AbstractController
{
    /**
     * @Route("/trouvez-une-sortie", name="app_event_research")
     */
    public function index(EntityManagerInterface $em,SortieRepository $sortieRepo): Response
    {
        $sorties = $sortieRepo->findSortiesOuverte(16);


        return $this->render('event_research/index.html.twig', [
            'controller_name' => 'EventResearchController',
            'listSortie'=>$sorties,

        ]);
    }

    //génération du formulaire de recherche
    public function searchForm(){
        $formResearch = $this->createFormBuilder()
            ->setAction($this->generateUrl('Search'))
            ->add('query', TextType::class, [
                'label' => false,
                'required'=>false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrez un mot-clé'
                ]
            ])
            ->add('recherche', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary'
                ]
            ])
            ->add('campus',EntityType::class, [
                'class' => Campus::class,
                'required'=>false,
                'choice_label' => 'nom',
                'placeholder' => 'Selectionner un campus',
                'label'=>'Campus :'
            ])
            ->add('categorie',EntityType::class, [
                'class' => Categorie::class,
                'required'=>false,
                'choice_label' => 'libelleCat',
                'placeholder' => 'Toutes les catégories',
                'label'=>'Categorie :'
            ])
            ->add('dateDebut', DateTimeType::class, [
                'widget' => 'single_text',
                'label'=>'Entre :',
                'required'=>false,
                'input'=> 'datetime',
                'input_format'=>'datetime'

            ])
            ->add('dateFin', DateTimeType::class, [
                'widget' => 'single_text',
                'label'=>'et :',
                'required'=>false,
                'html5'=>true,

            ])
            ->add('CBorganisateur', CheckboxType::class, [
                'label'=>"Sorties dont je suis l'organisateur/trice",
                'required'=>false,
                'empty_data'=>false,
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
            ])


            ->getForm();
        return $this->render('event_research/searchBar.html.twig', [
            'searchForm' => $formResearch->createView()
        ]);

    }

    /**
     * @Route("/recherche-sortie", name="Search")
     * @param Request $request
     */
    public function handleSearch(Request $request, SortieRepository $repo): Response
    {
        //dd($request->request->get('form'));
        $formRequest = $request->request->get('form');

        $query = $request->request->get('form')['query'];
        $cat = $request->request->get('form')['categorie'];
        $campus = $request->request->get('form')['campus'];
        $dateDebut = $request->request->get('form')['dateDebut'];
        $dateFin = $request->request->get('form')['dateFin'];
        $CBorga = null;
        $CBinscrit = null;
        $CBnonInscrit = null;
        $CBfini = null;

        if(array_key_exists('CBorganisateur', (array)$formRequest)){
            $CBorga = true;
        }
        if(array_key_exists('CBinscrit', (array)$formRequest)){
            $CBinscrit = true;
        }
        if(array_key_exists('CBnoninscrit', (array)$formRequest)){
            $CBnonInscrit = true;
        }
        if(array_key_exists('CBarchive', (array)$formRequest)){
            $CBfini = true;
    }

/*
        $CBorga = $request->request->get('form')['CBorganisateur'];
        $CBinscrit = $request->request->get('form')['CBinscrit'];
        $CBnonInscrit = $request->request->get('form')['CBnonInscrit'];
        $CBfini = $request->request->get('form')['CBarchive'];
*/
        $user = $this->getUser();


         $sorties = $repo->findSortiebyName( $query, $user, $campus , $cat , $dateDebut , $dateFin , $CBorga, $CBinscrit, $CBnonInscrit, $CBfini);

        return $this->render('event_research/index.html.twig', [
            'listSortie' => $sorties,
            'searchForm'=> $request->get('form')
        ]);
    }
}
