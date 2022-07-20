<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\User;
use App\Form\CreeLieuType;
use App\Form\CreeSortieType;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("/sortie", name="sortie_")
 */

class SortieController extends AbstractController
{


    /**
     * @Route("/cree", name="cree")
     */
    public function cree(Request $request,
                         EntityManagerInterface $entityManager,
                         UserRepository $userRepository,
                         EtatRepository $etatRepository,
                         SortieRepository $sortieRepository,
                         Security $security
                          ): Response
    {
        $sortie = new Sortie();
        $lieu = new Lieu();

        //TODO Recupérer l'instance de la personne connecté


        $userTest = $this->get('security.token_storage')->getToken()->getUser();
        $etat = $etatRepository->find(1);

        $sortie->setOrganisateur($userTest);
        $sortie->setEtat($etat);





        $formulaireSortie=$this->createForm(CreeSortieType::class, $sortie);
        $formulaireLieu=$this->createForm(CreeLieuType::class,$lieu );




        //TODO Seter avec le nom de l'utilisateur courant

        $formulaireSortie->handleRequest($request);
        $formulaireLieu->handleRequest($request);

        if($formulaireSortie->isSubmitted()&&$formulaireSortie->isValid()){
            $entityManager->persist($sortie);
            $entityManager->flush();

            $this->addFlash('success', 'Sortie ajoutée !');
        }


        if($formulaireLieu->isSubmitted()&&$formulaireLieu->isValid()){
            $entityManager->persist($lieu);
            $entityManager->flush();

            $this->addFlash('success', 'Lieu ajoutée !');
        }





        return $this->render('sortie/index.html.twig', [
            'formulaireSortie' =>  $formulaireSortie->createView(),
            'formulaireLieu' =>  $formulaireLieu->createView(),
        ]);
    }

    /**
     * @Route("/detail/{id}", name="detail")
     */
    public function detailSortie(Request $request,Sortie $sortie,SortieRepository  $sortieRepo,EntityManagerInterface $em):Response{

        $sortie = $sortieRepo->find($sortie);
        if(!$sortie){throw  $this->createNotFoundException('Sortie introuvable');}
        return $this-> render('sortie/detail.html.twig',['sortie' => $sortie]);
    }



}
